<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Support\Arr;

class Search
{

    public $draw         = false;
    public $start        = 0;
    public $limit        = 0;
    public $order        = [];
    public $search_value = '';
    public $request      = null;
    public $columns      = [];

    protected $input = [];

    public function fill($request, $custom_search_fields = [])
    {

        $this->request = $request;
        $this->draw    = $request->input('draw');
        $this->start   = $request->input('start');
        $this->limit   = $request->input('length');
        $this->order   = $request->input('order');

        $this->columns = $request->input('columns');

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

    public function columnName($column_number)
    {

        $column = Arr::get($this->columns, $column_number);

        return Arr::get($column, 'name');

    }
}