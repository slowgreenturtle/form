<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TrackChanges extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        $change_table_name = config('sgtform:config.change_table', 'model_changes');

        if (Schema::hasTable($change_table_name))
        {

            Schema::table($change_table_name, function (Blueprint $table) use ($change_table_name)
            {

                if (Schema::hasColumn($change_table_name, 'id'))
                {
                    $table->bigIncrements('id')->change();
                }
                else
                {
                    $table->bigIncrements('id');
                }

                if (Schema::hasColumn($change_table_name, 'reportable_id'))
                {
                    $table->unsignedBigInteger('reportable_id')->nullable()->change();
                }
                else
                {
                    $table->unsignedBigInteger('reportable_id')->nullable();

                }

                $table->string('reportable_type')->nullable()->change();
                $table->string('field')->change();
                $table->text('value')->nullable()->change();
                $table->unsignedBigInteger('user_id')->nullable()->change();

                if (!Schema::hasColumn($change_table_name, 'created_at'))
                {
                    $table->timestamp('created_at');

                }
            });

        }
        else
        {


            Schema::create($change_table_name, function (Blueprint $table)
            {

                $table->bigIncrements('id');
                $table->unsignedBigInteger('reportable_id')->nullable();
                $table->string('reportable_type')->nullable();
                $table->string('field');
                $table->text('value')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();

                $table->timestamp('created_at');
            });
        }

    }

}
