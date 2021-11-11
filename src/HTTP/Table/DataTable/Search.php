<?php

namespace SGT\HTTP\Table\DataTable;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Search
{

    public    $draw         = false;
    public    $start        = 0;
    public    $limit        = 0;
    public    $order        = [];
    public    $request      = null;
    public    $columns      = [];
    protected $session_data = null;

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

    public function fill(Request $request, $field_map = [])
    {

        $this->request = $request;

        $clear = $this->input('clear');

        if ($clear)
        {
            $this->sessionForget();
        }

        $this->session_data = session($this->session_name);

        $this->draw  = $this->input('draw');
        $this->start = $this->input('start');
        $this->limit = $this->input('length');
        $this->order = $this->input('order');

        $this->columns = $this->input('columns');

        if (Arr::has($this->input, 'text') == false && $this->request->has('text') == false && Arr::get($this->session_data, 'text') == null)
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
            $session_input = [];

            if ($this->session_data != null)
            {
                $session_input = $this->session_data;
            }

            $result = $input + $request_input + $session_input;

        }
        else
        {

            $result = Arr::get($this->input, $name);

            if ($result == null)
            {
                $result = $this->request->input($name, $default);

                if ($result == null && $this->session_data != null)
                {
                    $result = Arr::get($this->session_data, $name, $default);
                }

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

        $session_name = $this->session_name;
        session()->forget($session_name);

        $this->session_data = session($session_name);

    }

}
