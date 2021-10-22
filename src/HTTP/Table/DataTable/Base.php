<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use SGT\HTTP\SGTHtml;
use SGT\Traits\Config;
use stdClass;

abstract class Base
{

    use Config;

    /** the data url for the offer */
    public $data_url = '';

    public $default_limit = 100;

    /** the custom method to use to send custom data back to the server */
    public $data_method = '';

    /**  The html name base for this table */
    protected $name = 'table_name';
    /**  The Laravel view for this table */
    protected $view = null;
    /** @var string $view_file The Laravel view file */
    protected $view_file = '';

    protected $search = null;

    protected $settings = [
        'pageLength'  => 25,
        'responsive'  => true,
        'order'       => [[0, 'asc']],
        'ordering'    => true,
        'deferRender' => true,
        'processing'  => true,
        'serverSide'  => true,
        'saveState'   => true,
        'lengthMenu'  => [
            50,
            100,
            500
        ]
    ];

    /** @var Request|null The http request */
    protected $classes = [
        'wrapper' => [
            'dataTables_wrapper' => 'dataTables_wrapper',
            'form-inline'        => 'form-inline',
            'dt-bootstrap'       => 'dt-bootstrap',
            'no-footer'          => 'no-footer'
        ],
        'table'   => [
            'table'          => 'table',
            'table-striped'  => 'table-stripped',
            'table-bordered' => 'table-bordered',
            'table-hover'    => 'table-hover',
            'no-footer'      => 'no-footer'
        ],
        'row'     => []
    ];

    public function __construct(Request $request)
    {

        $data = ['session_name' => get_class($this)];

        $this->search = new Search($data);
        $this->search->fill($request);

        $this->html = new SGTHtml();
        $this->view = view($this->getViewFile());

        $this->setConfigSettings();

        if (property_exists($this, 'search_columns'))
        {

            $this->setting('searching', false);

            $setting_list = '';

            foreach ($this->search_columns as $setting)
            {
                $setting_list .= "d.{$setting}={$setting};";
            }

            $this->setting('ajax.data', $setting_list);
        }

        $this->setup();

    }

    public function sessionStore()
    {

        $this->search->sessionStore();

    }

    public function sessionForget()
    {

        $this->search->sessionForget();

    }

    public function getViewFile()
    {

        $view_file = $this->view_file;

        if (empty($view_file))
        {
            $view_file = $this->configFrontEnd('table.datatable.default');
        }

        return $view_file;

    }

    public function setConfigSettings()
    {

        $settings = $this->config('config.table.settings');

        $this->settings = array_merge($this->settings, $settings);

    }

    public function setup()
    {

    }

    /**
     * Use Dot notation to add a settings value to the array going to the javascript datatable settings.
     *
     * @param $field
     * @param $value
     */
    public function setting($field, $value)
    {

        Arr::set($this->settings, $field, $value);

    }

    public function getSetting($field, $default_value = null)
    {

        return Arr::get($this->settings, $field, $default_value);
    }

    public function getSearchInput($name = null, $value = null)
    {

        return $this->search->input($name, $value);

    }

    public function addSearchInput($name, $value)
    {

        $this->search->addInput($name, $value);

    }

    public function cssAdd($list, $value)
    {

        $this->classes[$list][] = $value;
    }

    public function cssRemove($list, $value)
    {

        unset($this->classes[$list][$value]);
    }

    /**
     * Export the View as an html page.
     *
     * @return string
     */
    public function html()
    {

        $this->view->table = $this;

        return $this->view->__toString();
    }

    /**
     * Return an array of data describing the header fields
     *
     * @return array
     */
    public function headers()
    {

        return $this->tableColumns();
    }

    /**
     * This method processes the  columns made by the user, sets defaults, etc.
     */
    public function tableColumns()
    {

        $fields = [
            'tooltip',
            'sort_field',
        ];

        $columns = $this->columns();

        $table_columns = [];

        foreach ($columns as $column_id => $column)
        {
            if (is_array($column))
            {
                $table_columns[$column_id]['name'] = Arr::get($column, 'name', ucwords(str_replace('_', ' ', $column_id)));

                foreach ($fields as $field)
                {
                    $table_columns[$column_id][$field] = Arr::get($column, $field, '');
                }
            }
            else
            {
                $table_columns[$column]['name'] = Arr::get($column, 'name', ucwords(str_replace('_', ' ', $column)));

                foreach ($fields as $field)
                {
                    $table_columns[$column][$field] = '';
                }
            }
        }

        return $table_columns;

    }

    /**
     * Return an array of columns.
     * This method will be overwritten by derived classes.
     *
     * @return array
     */

    public function columns()
    {

        /**
         *  Must be in the following format:
         * ['slug_name'] = [
         *  'name'=>'Name',
         *  'sort_field'=>'name'
         * ]
         * slug_name    must be lowercase, underscored, used internally to track the calls, sent to the client as id fields,
         *              etc
         * name         The human readable name, most notably used in the header field of the table.
         * sort_field   is the field used if this column is sortable and can be ordered by. Used in the query sort
         *              functionality
         */

        /**
         * example:
         * $columns = [
         * "id"      =>
         * [
         * 'name' => 'ID',
         * ],
         * "name"    => [
         * 'name' => 'Name'
         * ],
         * "feed"    => [
         * 'name'     => 'Feeds',
         * ],
         * "margin"  => [
         * 'name'     => 'Margin %',
         * ],
         * 'actions' => [
         * 'name'     => '',
         * ]
         * ];
         * */

        return [];
    }

    /**
     * Output the HTML table body content
     */
    public function body()
    {

        if ($this->getDataURL() != '')
        {
            //  we won't be loading data using this method
            return '';
        }

        $html = '';

        $data = $this->data();

        $records = Arr::get($data, 'data');

        $row_class = $this->htmlClass('row');

        $row_css_html = empty($row_class) ? '' : ('class="' . $row_class . '"');

        foreach ($records as $record)
        {

            $html .= '<tr role="row"' . $row_css_html . '>';

            foreach ($this->tableColumns() as $column_name => $column)
            {

                $html .= '<td>';

                $columnName = 'column_' . $column_name;

                if (method_exists($this, $columnName))
                {
                    $html .= $this->$columnName($record);
                }
                else
                {
                    $html .= '&nbsp;';
                }

                $html .= '</td>';

            }

            $html .= '</tr>';
        }

        return $html;

    }

    public function getDataURL()
    {

        return $this->data_url;
    }

    /**
     * The full URL to retrieve data from.
     *
     * @param $url
     */
    public function setDataURL($url)
    {

        $this->data_url = $url;

    }

    public function data()
    {

        $record_count        = 0;
        $record_filter_count = 0;

        #   Get the raw query records
        $query = $this->query();

        if ($query)
        {
            $record_count = $query->count();
        }

        # append the search query
        $query = $this->querySearch($query);
        $query = $this->querySetOrder($query);

        if ($query)
        {
            $record_filter_count = $query->count();
        }

        $query = $this->queryLimit($query);

        # take the search records and return the formatted result
        $records = $this->querySearchRecords($query);

        # take the formatted results and create the field elements.
        $results = $this->formattedRecords($records);

        $data['data']            = $results;
        $data['recordsTotal']    = $record_count;
        $data['recordsFiltered'] = $record_filter_count;

        return $data;
    }

    public function query()
    {

        return null;
    }

    /**
     * @param $query
     *              Return the query with the search filters appended to it.
     *
     * @return mixed
     */
    public function querySearch($query)
    {

        return $query;

    }

    public function querySetOrder($query)
    {

        $sort_directions = [
            'asc'  => 'asc',
            'desc' => 'desc'
        ];

        $order = $this->search->order;

        if (!is_array($order))
        {
            return $query;
        }

        foreach ($order as $order_item)
        {

            $column_number = Arr::get($order_item, 'column', '');

            if (is_numeric($column_number))
            {

                $order_column = Arr::get($this->search->columns, $column_number);

                if ($order_column)
                {

                    $column_name = Arr::get($order_column, 'name');

                    if (empty($column_name))
                    {
                        continue;
                    }

                    $sort_direction = Arr::get($order_item, 'dir', 'asc');

                    $sort_direction = Arr::get($sort_directions, $sort_direction, 'asc');

                    $orderMethodName = 'order_' . $column_name;

                    if (method_exists($this, $orderMethodName))
                    {
                        $this->$orderMethodName($query, $sort_direction);
                    }
                    else
                    {

                        $column = $this->column($column_name);

                        if ($column)
                        {
                            $sort_column = Arr::get($column, 'sort_field');

                            if ($sort_column)
                            {
                                $query->orderBy($sort_column, $sort_direction);
                            }

                        }
                    }
                }
            }

        }

        return $query;
    }

    public function column($column_name)
    {

        return Arr::get($this->tableColumns(), $column_name);
    }

    public function queryLimit($query)
    {

        if ($query == null)
        {
            return $query;
        }

        $limit = $this->search->limit;

        if (empty($limit) || $limit < 1)
        {
            $limit = $this->default_limit;
        }

        $query->limit($limit);

        $start = $this->search->start;

        if (!empty($start))
        {
            $query->offset($start);
        }

        return $query;

    }

    /**
     * Retrieve the list of records used for this display, filtered by search variables.
     *
     * @return array
     */
    public function querySearchRecords($query_search)
    {

        if ($query_search)
        {
            return $query_search->get();
        }

        return [];
    }

    /**
     * Retrieve the records formatted by their column_* calls.
     *
     * @return array
     */
    protected function formattedRecords($records)
    {

        $results = [];

        foreach ($records as $record)
        {
            $fields = [];

            foreach ($this->tableColumns() as $column_name => $column)
            {
                $columnName = 'column_' . $column_name;

                if (method_exists($this, $columnName))
                {
                    $content = $this->$columnName($record);
                }
                else
                {

                    $value   = $record->$column_name;
                    $content = $value == null ? '&nbsp;' : $value;
                }

                $fields[] = $content;
            }

            $results[] = $fields;
        }

        return $results;

    }

    /**
     * Retrieve a CSS class list ready to be inserted into an html element.
     *
     * @param $group
     *
     * @return string
     */
    public function htmlClass($group)
    {

        return implode(' ', $this->classes[$group]);
    }

    /**
     * Returns a list of css classes.
     *
     * @param $list
     *
     * @return mixed
     */
    public function cssClass($list)
    {

        $classes = Arr::get($this->classes, $list, []);

        return $classes;

    }

    /**
     * Creates a name for the table and any sub elements required
     *
     * @param string $append
     *
     * @return mixed|string
     */
    public function name($append = '')
    {

        $name = get_called_class();

        $name = str_replace('\\', '_', $name);

        $name .= '_' . $this->name;

        if (!empty($append))
        {
            $name .= '_' . $append;
        }

        return $name;

    }

    public function results($request)
    {

        $this->request = $request;

        //$total = $this->total();
        //$count = $this->count();

        $total   = 0;
        $count   = 0;
        $results = [];

        $data = [
            'draw'            => 'full-hold',
            'recordsTotal'    => $total,
            'recordsFiltered' => $count,
            'data'            => $results
        ];

        return response()->json($data);
    }

    public function jsSettings()
    {

        $settings = $this->settings;

        $settings['columns'] = $this->jsColumns();

        $data_url = $this->getDataURL();

        if ($data_url != '')
        {
            $settings['ajax']['url'] = $data_url;
        }

        if ($this->data_method != '')
        {
            $settings['ajax']['data'] = $this->data_method;
        }

        return $settings;
    }

    /**
     *  A list of column which should be sorted in the view
     *
     * @return array
     */
    public function jsColumns()
    {

        $results = [];

        $columns = $this->tableColumns();

        foreach ($columns as $column_name => $column)
        {

            $result            = new stdClass();
            $result->orderable = $this->sortable($column_name);
            $result->name      = $column_name;

            $results[] = $result;
        }

        return $results;

    }

    public function sortable($column_name)
    {

        if (Arr::get($this->column($column_name), 'sort_field') != null)
        {
            return true;
        }

        $columnName = 'order_' . $column_name;

        if (method_exists($this, $columnName))
        {
            return true;
        }

        return false;
    }

    public function response($store_session = true)
    {

        if ($store_session == true)
        {
            $this - $this->sessionStore();
        }

        $clear = $this->getSearchInput('clear');

        if ($clear)
        {
            $this->sessionForget();

            return '';
        }

        $data = $this->serverData();

        return response()->json($data);

    }

    public function serverData()
    {

        $data = $this->data();

        $data['draw'] = $this->search->draw;

        return $data;
    }

    public function scripts()
    {

        return '';
    }

}
