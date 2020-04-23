<?php

namespace SGT;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use SGT\Model\Cloud;
use SGT\Traits\Config;
use ZipArchive;

class DataManage
{

    use Config;

    protected $system_connection = null;
    protected $multi_tenant      = false;

    protected $tenant_connection = 'team';

    protected $delete_limit      = 1;    //  how many days in the past do we want to remove?
    protected $backup_to_ftp     = false;    //App/config/ftp.php must be configured for this to work properly.
    protected $s3_backup_path    = 'backup';
    protected $local_backup_path = 'database';  # local storage relative backup path.

    public function __construct($messenger = null)
    {

        $this->system_connection = $this->config('database.connection');

        $this->tenant_connection = $this->config('database.tenant.connection');
        $this->multi_tenant      = $this->config('database.tenant.enabled');

        $this->local_backup_path = $this->config('database.backup.path');
        $this->delete_limit      = $this->config('database.backup.days_stored', 1);
        $this->s3_backup_path    = $this->config('database.backup.s3_path');

        $this->messenger = $messenger;

        $this->tenant_table_prefix   = config("database.{$this->tenant_connection}.prefix");
        $this->tenant_migration_path = config("database.{$this->tenant_connection}.migration.path");

        $this->tenant_database_name_field = config("database.connections.{$this->tenant_connection}.database");

    }

    /**
     * Truncate database tables
     *
     * @throws \ErrorException
     */

    public function truncateTables()
    {

        $db_name = config('database.connections.' . $this->system_connection . '.database');

        $this->truncateDatabaseTables($this->system_connection, $db_name);

        $this->info("System database '$db_name' tables truncated");

        if ($this->multi_tenant == true)
        {

            $database_count = 1;

            $databases = $this->getTenantDatabases();

            foreach ($databases as $database)
            {

                $this->setConnection($database);
                $this->truncateDatabaseTables($this->tenant_connection, $database, true);

                $database_count++;
            }

            $this->info("Truncated $database_count databases");
        }

    }

    /**
     * Truncate all tables within the requested database, but leave the schema.
     *
     * @param $connection
     * @param $database_name
     */
    protected function truncateDatabaseTables($connection, $database_name)
    {

        //  We put this here in case the database doesn't exist, otherwise throws off the table check.
        $result = DB::connection($connection)->select("SHOW DATABASES LIKE '{$database_name}';");

        if (count($result) < 1)
        {
            $this->info("Cannot remove tables. Database doesn't exist.");

            return;
        }

        $this->info("Truncating tables from $database_name. Connection: $connection");

        $tables = $this->getTables($connection, $database_name);

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table_name)
        {
            //if you don't want to truncate migrations
            if ($table_name == 'migrations')
            {
                continue;
            }
            DB::connection($connection)->table($table_name)->truncate();
        };

        Schema::enableForeignKeyConstraints();
    }

    public function info($message)
    {

        if ($this->messenger)
        {
            $this->messenger->info($message);
        }
    }

    public function getTables($connection, $database): array
    {

        $tables = DB::connection($connection)->select("SHOW TABLES FROM $database;");

        $list = [];

        foreach ($tables as $table)
        {

            $table_array = (array)$table;

            $list[] = array_pop($table_array);
        };

        return $list;
    }

    protected function getTenantDatabases()
    {

        $prefix = $this->tenant_table_prefix;

        if (empty($prefix))
        {
            throw new \ErrorException('No Multi-tenant Prefix set');
        }

        $databases = DB::select("SHOW DATABASES LIKE '$prefix%'");

        $results = [];

        foreach ($databases as $database)
        {
            $results[] = array_pop($database);
        }

        return $results;
    }

    public function setConnection($db_name, $set_default = false)
    {

        $this->setConnectionDatabase($this->tenant_connection, $db_name);

        DB::reconnect($this->tenant_connection); // Reconnect to this connection to avoid loading a cached version

        if ($set_default == true)
        {
            DB::setDefaultConnection($this->tenant_connection); // Set the default connection to this new connection
        }

    }

    public function setConnectionDatabase($connection, $name)
    {

        Config::set("database.connections.{$connection}.database", $name);

    }

    /**
     * Make a clean copy of the database store it zipped locally in the storage path.
     * Exports by default.
     *
     * @param $params
     */
    public function copy($params = [])
    {

        $type = Arr::get($params, 'type', 'export');
        if ($type == 'import')
        {
            # import the connection
            $this->databaseImport($params);
        }
        else
        {
            $this->databaseExport($params);
        }

    }

    /**
     * Import the clean_<database name> copy of the active database.
     *
     * @param $params
     */
    protected function databaseImport($params)
    {

        $storage       = Arr::get($params, 'storage', 'local');
        $return_status = true;

        $local_filename = Arr::get($params, 'filename', $this->config('database.copy.local.filename'));
        $cloud_filename = Arr::get($params, 'filename', $this->config('database.copy.cloud.filename'));
        $connection     = Arr::get($params, 'connection', config('database.default'));

        $database_name = config("database.connections.{$connection}.database");

        $local_path = storage_path($this->config('database.copy.local.path'));
        $local_file = $local_path . DIRECTORY_SEPARATOR . $local_filename . '.zip';

        $cloud_path = $this->config('database.copy.cloud.path');
        $cloud_file = $cloud_path . DIRECTORY_SEPARATOR . $cloud_filename . '.zip';

        @mkdir($local_path);

        $this->info("Connection: $connection");

        #export the connection
        $message = "Importing `$database_name` from ";

        if ($storage == 'local')
        {
            $message .= "`$local_file`";
            $this->info($message);

            if (file_exists($local_file) == false)
            {
                $this->info("Local file doesn't exist");

                return false;
            }

        }
        else
        {
            $message .= "`$cloud_file`";
            $this->info($message);

            $disk = $this->config('database.copy.cloud.disk');

            $file_exists = Cloud::exists($cloud_file, $disk);

            if ($file_exists == false)
            {
                $this->info("Cloud file doesn't exist");

                return false;

            }

            $file_contents = Cloud::get($cloud_file, $disk);
            file_put_contents($local_file, $file_contents);

        }

        # extract the zip file in the temp folder
        $zip = new ZipArchive();

        $tmp_dir = DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR . $database_name;

        $this->info("Extracting `$local_file` to `$tmp_dir`");

        try
        {

            if ($zip->open($local_file) === true)
            {
                $zip->extractTo($tmp_dir);
                $zip->close();
            }
            else
            {
                throw new \Exception("Couldn't find local backup file: $local_file");
            }

            $this->deleteTables($connection, $database_name);

            # import the schema
            $file_destination = "< $tmp_dir" . DIRECTORY_SEPARATOR . "schema.sql";

            $parameters = [
                'command'     => 'mysql',
                'connection'  => $connection,
                'database'    => $database_name,
                'destination' => $file_destination,
            ];

            $this->mySQL($parameters);

            # import each table

            Schema::disableForeignKeyConstraints();

            $full_path = $tmp_dir . DIRECTORY_SEPARATOR . 'table_*';

            $prefix = $tmp_dir . DIRECTORY_SEPARATOR . 'table_';

            foreach (glob($full_path) as $file_name)
            {

                $table_name = str_replace([$prefix, '.sql'], '', $file_name);

                $this->info("Restoring table : $table_name");

                $file_destination = "< $tmp_dir" . DIRECTORY_SEPARATOR . "table_{$table_name}.sql";

                $parameters = [
                    'command'     => 'mysql',
                    'connection'  => $connection,
                    'database'    => $database_name,
                    'destination' => $file_destination,
                    'table'       => [$table_name]
                ];

                $this->mySQL($parameters);

            }

            Schema::enableForeignKeyConstraints();

            # enable foreign key constraints

        }
        catch (\Exception $e)
        {
            $this->info($e->getMessage());

            $return_status = false;
        }

        File::deleteDirectory($tmp_dir);

        return $return_status;
    }

    protected function deleteTables($connection, $database_name, $drop_db = false)
    {

        //  We put this here in case the database doesn't exist, otherwise throws off the table check.
        $result = DB::connection($connection)->select("SHOW DATABASES LIKE '{$database_name}';");

        if (count($result) < 1)
        {
            $this->info("Cannot remove tables. Database doesn't exist.");

            return;
        }

        $this->info("Dropping tables from $database_name. Connection: $connection");

        $tables = $this->getTables($connection, $database_name);

        Schema::disableForeignKeyConstraints();

        foreach ($tables as $table_name)
        {
            Schema::connection($connection)->dropIfExists($table_name);
        };

        Schema::enableForeignKeyConstraints();

        if ($drop_db == true)
        {
            DB::connection($connection)->statement("DROP DATABASE IF EXISTS $database_name;");
        }

    }

    /**
     * Wrapper for the mysql dump command.
     *
     * @param array $params
     *                     'connection'
     *                     'database'
     *                     'destination'
     *                     'parameters'
     *                     'tables'
     */
    public function mySQL(array $params)
    {

        $command = Arr::get($params, 'command', 'mysql');

        $connection  = Arr::get($params, 'connection', '');
        $database    = Arr::get($params, 'database', '');
        $destination = Arr::get($params, 'destination', '');
        $parameters  = Arr::get($params, 'parameters', []);
        $tables      = Arr::get($params, 'tables', []);

        $db_username = config('database.connections.' . $connection . '.username');
        $db_password = config('database.connections.' . $connection . '.password');
        $db_host     = config('database.connections.' . $connection . '.host');

        $dump_parameters[] = $command;
        $dump_parameters[] = "-h $db_host";
        $dump_parameters[] = "-u $db_username";

        $dump_parameters = array_merge($dump_parameters, $parameters);

        $dump_parameters[] = $database;

        $dump_parameters[] = implode(' ', $tables);

        if ($destination)
        {
            $dump_parameters[] = $destination;
        }

        $command = implode(' ', $dump_parameters);

        putenv('MYSQL_PWD=' . $db_password);
        $response = shell_exec($command);
        putenv('MYSQL_PWD=');

    }

    /**
     * Export a database
     */
    protected function databaseExport($params)
    {

        # Export the file at this point.
        $local_filename = Arr::get($params, 'filename', $this->config('database.copy.local.filename'));
        $cloud_filename = Arr::get($params, 'filename', $this->config('database.copy.cloud.filename'));
        $connection     = Arr::get($params, 'connection', config('database.default'));

        # Export the database to a local file or a cloud file.
        $storage = Arr::get($params, 'storage', 'local');

        $local_path = storage_path($this->config('database.copy.local.path'));

        $database_name = config("database.connections.{$connection}.database");

        $cloud_path = $this->config('database.copy.cloud.path');
        $cloud_file = $cloud_path . DIRECTORY_SEPARATOR . $cloud_filename . '.zip';

        # export the zip file to the local path.
        $local_file = $local_path . DIRECTORY_SEPARATOR . $local_filename . '.zip';

        #export the connection
        $message = "Exporting `$database_name` to ";

        if ($storage == 'local')
        {
            $message .= "`$local_file`";
        }
        else
        {
            $message .= "`$cloud_file`";
        }

        $this->info("Connection: $connection");
        $this->info($message);

        @mkdir($local_path);

        # necessary because the zipfile must exist for the ziparchive to have something to open.
        # Delete the local file because we're store it there first.
        @unlink($local_file);

        # reopen the local file as a 'new' file.
        $file = fopen($local_file, 'w');
        fclose($file);

        # then open the local file as a zip archive
        $zip = new ZipArchive();
        $zip->open($local_file, ZipArchive::OVERWRITE);

        # when 'adding' files to the zip archive, the actual add doesn't happen until the close call.
        # the temp files can then be deleted.
        # we store the list of names here.
        $removeFiles = [];

        #export schema
        $parameters = [
            '--no-data',
        ];

        $random_filename = Str::random(20);

        $schema_file = "/tmp/{$database_name}_{$random_filename}.sql";

        $removeFiles[] = $schema_file;

        $output_command = "> $schema_file";

        $dump_params = [
            'command'     => 'mysqldump',
            'parameters'  => $parameters,
            'connection'  => $connection,
            'database'    => $database_name,
            'destination' => $output_command
        ];

        $this->mySQL($dump_params);

        // Add schema to zip file
        $zip->addFile($schema_file, 'schema.sql');

        $this->addTablesToCopy($connection, $database_name, $zip, $params, $removeFiles);

        // Close and send to users
        $zip->close();

        # remove all the temp files
        $this->info("Cleaning up temp files");

        foreach ($removeFiles as $remove_file)
        {
            unlink($remove_file);
        }

        $this->info("Finished exporting local database");

        # if the export is cloud type, copy the local file to the cloud and remove the local file.

        if ($storage == 'cloud')
        {

            $disk = $this->config('database.copy.cloud.disk');
            $this->cloudCopy($local_file, $cloud_file, 'private', $disk);
            # delete the local copy
            unlink($local_file);
        }
    }

    protected function addTablesToCopy($connection, $database_name, $zip, $params, &$removeFiles)
    {

        $db_username = config('database.connections.' . $connection . '.username');
        $db_password = config('database.connections.' . $connection . '.password');
        $db_host     = config('database.connections.' . $connection . '.host');

        $command = "mysql -h {$db_host} -u{$db_username} $database_name ";

        $tables = $this->getTables($connection, $database_name);

        $exclude_list = Arr::get($params, 'schema_only', []);

        $filtered_list = Arr::get($params, 'filtered');

        /**
         * @todo Fix this so filtered tables are properly filtered.
         *       as of now the entire table will be moved over.
         */

        $filtered_list = [];

        foreach ($tables as $table)
        {

            if (in_array($table, $exclude_list))
            {
                continue;
            }

            # run the custom SQL to query for only the required records.
            $filter = Arr::get($filtered_list, $table);

            $random = Str::random(10);

            $file = "/tmp/{$table}_{$random}.sql";

            if ($filter)
            {

                $sql = ($filter)();

                if (empty($sql))
                {
                    continue;
                }

                $file = "/var/lib/mysql-files/{$table}_{$random}.csv";
                $file = "/tmp/{$table}_{$random}.csv";

                $sql .= " INTO OUTFILE '{$file}' FIELDS TERMINATED BY ',';";

                $execute = $command . "-e\"$sql\"";

                //$execute = $command . "-e\" $sql \" > $file";

                putenv('MYSQL_PWD=' . $db_password);
                $response = shell_exec($command);
                putenv('MYSQL_PWD=');
            }
            else
            {

                # We want the whole table. Let's grab it.
                $file_destination = "> $file";

                $parameters = [
                    'command'     => 'mysqldump',
                    'connection'  => $connection,
                    'database'    => $database_name,
                    'destination' => $file_destination,
                    'tables'      => [$table]
                ];

                $this->mySQL($parameters);

            }

            $zip->addFile($file, "table_{$table}.sql");

            $removeFiles[] = $file;

        }
    }

    /**
     * @param $local_file the local file including path.
     * @parm $cloud_file the cloud file including path
     */

    public function cloudCopy($local_file, $cloud_file, $visibility = 'private', $disk = 's3')
    {

        if (is_file($local_file))
        {
            $contents = file_get_contents($local_file);
            Cloud::put($cloud_file, $contents, $visibility, $disk);
            $this->info("Backed up $cloud_file to cloud destination");

        }
    }

    public function restore()
    {

        $this->checkPath();

        $db_name = config('database.connections.' . $this->system_connection . '.database');

        if (!$this->restoreDatabase($db_name, $this->system_connection))
        {
            return;
        }

        $this->info('Restored system database');
    }

    protected function checkPath()
    {

        $path = $this->getBackupPath();

        if (!is_dir($path))
        {
            mkdir($path);
        }
    }

    protected function getBackupPath()
    {

        return storage_path($this->local_backup_path);
    }

    protected function restoreDatabase($db_name, $connection)
    {

        $db_username = config('database.connections.' . $connection . '.username');
        $db_password = config('database.connections.' . $connection . '.password');
        $db_host     = config('database.connections.' . $connection . '.host');

        $db_full_path = $this->getFileName($db_name);

        if (file_exists($db_full_path))
        {

            $this->info("Restoring: $db_name");

            $this->info("Filename: " . $db_full_path);

            $this->deleteTables($connection, $db_name);

            //$command = "gzip -d < $db_full_path | mysql -h {$db_host} -u{$db_username} -p'$db_password' $db_name";
            $command = "gzip -d < $db_full_path | mysql -h {$db_host} -u{$db_username} $db_name";

            putenv('MYSQL_PWD=' . $db_password);
            $response = shell_exec($command);
            putenv('MYSQL_PWD=');

            return true;
        }
        else
        {

            $restore_path = $this->getBackupPath();
            $this->error("Database '$db_name' file '$db_full_path' doesn't exist in restore path '$restore_path");

            return false;
        }
    }

    protected function getFileName($db_name)
    {

        $path = $this->getBackupPath();

        $full_path = $path . '/db_' . $db_name . '*';

        $this->info($full_path);

        /*
         * Find the most recent file with the db_name pattern in it.
         */

        $latest_ctime    = 0;
        $latest_filename = false;

        foreach (glob($full_path) as $filename)
        {
            // could do also other checks than just checking whether the entry is a file
            if (is_file($filename) && filemtime($filename) > $latest_ctime)
            {
                $latest_ctime    = filemtime($filename);
                $latest_filename = $filename;
            }
        }

        return $latest_filename;

    }

    public function error($message)
    {

        if ($this->messenger)
        {
            $this->messenger->error($message);
        }
    }

    public function truncate()
    {

        $db_name = config('database.connections.' . $this->system_connection . '.database');

        $this->deleteTables($this->system_connection, $db_name);

        $this->info("System database '$db_name' truncated");

        if ($this->multi_tenant == true)
        {

            $database_count = 1;

            $databases = $this->getTenantDatabases();

            foreach ($databases as $database)
            {

                $this->setConnection($database);
                $this->deleteTables($this->tenant_connection, $database, true);

                $database_count++;
            }

            $this->info("Truncated $database_count databases");
        }
    }

    public function backup()
    {

        $this->checkPath();

        if ($this->backup_to_ftp == true)
        {
            $ftp_config = config('ftp');

            if ($ftp_config == null)
            {
                $this->error("FTP is not configured.");

                return;
            }
        }

        $this->backupSystem();

        $this->backupTenants();

        //  Clear old files in the directory
        $this->deleteOldFiles();

    }

    protected function backupSystem()
    {

        $connection_text = "database.connections.{$this->system_connection}.database";

        $db_name = config($connection_text);

        $this->backupDatabase($db_name, $this->system_connection);
    }

    protected function backupDatabase($db_name, $tenant_connection)
    {

        $this->info("Backing up: $db_name");

        $path = $this->getBackupPath();

        $db_username = config('database.connections.' . $tenant_connection . '.username');
        $db_password = config('database.connections.' . $tenant_connection . '.password');
        $db_host     = config('database.connections.' . $tenant_connection . '.host');

        $timestamp = time();

        $db_filename = "db_" . $db_name . '_' . Date('Y_m_d') . "_$timestamp.sql.gz";

        $db_full_path = "$path/$db_filename";

        //$command  = "mysqldump -h {$db_host} -u{$db_username} -p'$db_password' $db_name | gzip > $db_full_path";
        $command = "mysqldump -h {$db_host} -u{$db_username} $db_name | gzip > $db_full_path";

        putenv('MYSQL_PWD=' . $db_password);
        $response = shell_exec($command);
        putenv('MYSQL_PWD=');

        $this->backupToFTP($db_filename, $db_full_path);

    }

    protected function backupToFTP($ftp_file_name, $source_file)
    {

        if ($this->backup_to_ftp == false)
        {
            return;
        }

        $ftp = new Ftp(config('ftp'));

        if (!$ftp->login(config('ftp.username'), config('ftp.password')))
        {
            $this->info("Failed logging in");
        }
        else
        {

            $ftp->pasv(true);

            $ftp_path = config('ftp.path', '/');

            $ftp->chdir($ftp_path);

            $ftp->put($ftp_file_name, $source_file, FTP_ASCII);
        }

        $ftp->close();
    }

    protected function backupTenants()
    {

        if ($this->multi_tenant == false)
        {
            return;
        }

        $database_count = 0;

        $prefix = $this->tenant_table_prefix;

        $databases = DB::select("SHOW DATABASES LIKE '$prefix%'");

        foreach ($databases as $database)
        {
            $db_name = array_pop($database);

            $this->backupDatabase($db_name, $this->tenant_connection);
            $database_count++;
        }

        $this->info("Backed up $database_count tenant databases");

    }

    protected function deleteOldFiles()
    {

        $path = $this->getBackupPath();

        $files = glob($path . '/db_*.gz');
        $time  = time();

        foreach ($files as $file)
        {
            if (is_file($file))
            {
                if ($time - filemtime($file) >= 60 * 60 * 24 * $this->delete_limit)
                {
                    $this->info('Removing old backup:' . $file);
                    unlink($file);
                }
            }
        }
    }
}
