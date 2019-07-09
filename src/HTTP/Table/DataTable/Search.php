<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Support\Arr;

class Search
{

    public    $draw             = false;
    public    $start            = 0;
    public    $limit            = 0;
    public    $sort_column_name = '';
    public    $sort_column      = -1;
    public    $sort_order       = 'ASC';
    public    $search_value     = '';
    public    $request          = null;
    protected $input            = [];

    public function fill($request, $custom_search_fields = [])
    {


        $this->request          = $request;
        $this->draw             = $request->input('draw');
        $this->start            = $request->input('start');
        $this->limit            = $request->input('length');
        $this->sort_order       = $request->input('order.0.dir');
        $this->sort_column      = $request->input('order.0.column');
        $sort_column            = $this->sort_column;
        $this->sort_column_name = $request->input("columns.$sort_column.data");

        $this->addInput('text', $request->input('search.value'));

        foreach ($custom_search_fields as $field)
        {
            $this->addInput($field, $request->input($field));
        }
    }

    public function addInput($name, $value)
    {

        $this->input[$name] = $value;
    }

    public function input($name, $default = null)
    {

        return Arr::get($this->input, $name, $default);
    }
}