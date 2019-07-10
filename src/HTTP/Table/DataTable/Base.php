<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use SGT\HTTP\Config;
use SGT\HTTP\HtmlBuilder;
use stdClass;

abstract class Base
{

    use Config;

    public $length_menu = [50, 100, 500];
    /**  The number of rows to display by default */
    public $display_count = null;

    /** @var bool $save_state Save the last search values */
    public $save_state = true;

    /** the data url for the offer */
    public $data_url = '';

    /** the custom method to use to send custom data back to the server */
    public $data_method = '';

    /** @var bool $ordering Whether data table fields can be sorted */
    public $ordering = true;

    /** @var array $initial_order Initial order for the table column. To use multiple columns, nest array. */
    public $initial_order = [[0, 'asc']];

    /** @var bool $defer_render defer drawing fields when displaying large amounts of output */
    public $defer_render = true;
    /** @var bool $processing whether the server side should process data */
    public $processing = true;
    /** @var bool $server_side Enable Server side processing. Will use the data url to retrieve requests */
    public $server_side = true;
    /**  The html name base for this table */
    protected $name = 'table_name';
    /**  The Laravel view for this table */
    protected $view = null;
    /** @var string $view_file The Laravel view file */
    protected $view_file = '';

    protected $search = null;

    /** @var array Custom search fields the child model may want to get from the Request */
    protected $custom_search_fields = [];

    /** @var Request|null The http request */
    protected $classes =
        [
            'wrapper' =>
                [
                    'dataTables_wrapper' => 'dataTables_wrapper',
                    'form-inline'        => 'form-inline',
                    'dt-bootstrap'       => 'dt-bootstrap',
                    'no-footer'          => 'no-footer'
                ],
            'table'   =>
                [
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

        $this->search = new Search();
        $this->search->fill($request, $this->custom_search_fields);

        $this->html = new HtmlBuilder();
        $this->view = view($this->getViewFile());

        $this->display_count = $this->display_count == null ? $this->config('table.display_count') : $this->display_count;

        $this->length_menu = $this->config('table.length_menu');

        $this->setup();

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

    public function setup()
    {

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

        $fields = [
            'name',
            'tooltip'
        ];

        $columns = $this->columns();

        $table_headers = [];

        foreach ($columns as $column)
        {
            foreach ($fields as $field)
            {
                $header[$field] = array_get($column, $field, '');
            }
            $table_headers[] = $header;
        }

        return $table_headers;

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
         *
         * ['slug_name'] = [
         *  'name'=>'Name',
         *  'sort_field'=>'name'
         *
         *
         * ]
         *
         *
         * slug_name    must be lowercase, underscored, used internally to track the calls, sent to the client as id fields,
         *              etc
         * name         The human readable name, most notably used in the header field of the table.
         * sortable     Whether the field is sortable and has the up/down toggle showing. true by default.
         * sort_field   is the field used if this column is sortable and can be ordered by. Used in the query sort
         *              functionality
         *
         *
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
         * 'sortable' => false
         * ],
         * "margin"  => [
         * 'name'     => 'Margin %',
         * 'sortable' => false
         * ],
         * 'actions' => [
         * 'name'     => '',
         * 'sortable' => false
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

        if ($this->data_url != '')
        {
            //  we won't be loading data using this method
            return '';
        }

        $html = '';

        $records = $this->records();

        $row_class = $this->htmlClass('row');

        $row_css_html = empty($row_class) ? '' : ('class="' . $row_class . '"');

        foreach ($records as $record)
        {

            $html .= '<tr role="row"' . $row_css_html . '>';

            foreach ($this->columns() as $column_name => $column)
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

    /**
     * Retrieve a CSS class list ready to be inserted into an html element.
     *
     * @param $group
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

    /**
     * Output elements used for the table headers
     *
     * @return array
     */
    public function htmlHeaders()
    {

        $columns = $this->columns();

        $headers = [];

        foreach ($columns as $column_id => $details)
        {

            $item = [
                'name'    => Arr::get($details, 'name', ''),
                'tooltip' => Arr::get($details, 'tooltip'),
            ];

            $headers[] = $item;

        }

        return $headers;

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

        $settings = [];

        $settings['iDisplayLength'] = $this->display_count;
        $settings['responsive']     = true;
        $settings['order']          = $this->initial_order;
        $settings['ordering']       = $this->ordering;
        $settings['deferRender']    = $this->defer_render;
        $settings['processing']     = $this->processing;
        $settings['serverSide']     = $this->server_side;
        $settings['stateSave']      = $this->save_state;
        $settings['lengthMenu']     = $this->length_menu;
        $settings['columns']        = $this->jsColumns();

        if ($this->data_url != '')
        {
            $settings['ajax']['url'] = $this->data_url;
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

        $columns = $this->columns();

        foreach ($columns as $column_name => $column)
        {

            $result            = new stdClass();
            $result->orderable = Arr::get($column, 'sortable', true);
            $result->name      = $column_name;

            $results[] = $result;
        }

        return $results;

    }

    public function response()
    {

        $data = $this->serverData();

        return response()->json($data);

    }

    public function serverData()
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
        $data['draw']            = $this->search->draw;
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
                        $content = $this->$orderMethodName($query, $sort_direction);
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

        return Arr::get($this->columns(), $column_name);
    }

    public function queryLimit($query)
    {

        $limit = $this->search->limit;

        if (!empty($limit))
        {
            $query->limit($limit);
        }

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

            foreach ($this->columns() as $column_name => $column)
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

    public function scripts()
    {

        return '';
    }

}