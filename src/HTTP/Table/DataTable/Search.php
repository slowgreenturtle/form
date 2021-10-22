<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Support\Arr;

class Search
{

    public $draw    = false;
    public $start   = 0;
    public $limit   = 0;
    public $order   = [];
    public $request = null;
    public $columns = [];

    protected $session_name = 'default';
    protected $input        = [];
    public    $except       = [
        'draw',
        'start',
        'length',
        'order',
        'columns'
    ];

    public function __construct($data = [])
    {

        $this->session_name = Arr::get($data, 'session_name', 'default');
    }

    public function fill($request, $field_map = [])
    {

        $this->request = $request;
        $this->draw    = $this->input('draw');
        $this->start   = $this->input('start');
        $this->limit   = $this->input('length');
        $this->order   = $this->input('order');

        $this->columns = $this->input('columns');

        $session = session($this->session_name);

        if (is_array($session))
        {
            $this->input = $session;
        }

        if (Arr::has($this->input, 'text') == false && $this->inputrequest->has('text') == false)
        {
            $field_map['search.field'] = 'text';
        }

        foreach ($field_map as $search_field => $map_field)
        {
            $this->addInput($map_field, $this->request->input($search_field));
        }

    }

    public function addInput($name, $value)
    {

        $this->input[$name] = $value;
    }

    public function input($name = null, $default = null)
    {

        if ($name == null)
        {

            $input         = $this->input;
            $request_input = $this->request->except($this->except);

            $result = $input + $request_input;

        }
        else
        {

            $result = Arr::get($this->input, $name);

            if ($result == null)
            {
                $result = $this->request->input($name, $default);
            }
        }

        return $result;

    }

    public function columnName($column_number)
    {

        $column = Arr::get($this->columns, $column_number);

        return Arr::get($column, 'name');

    }

    public function sessionStore()
    {

        $store_data[$this->session_name] = $this->request->except($this->except);
        session($store_data);

    }

    public function sessionForget()
    {

        session()->forget($this->session_name);

    }

}
