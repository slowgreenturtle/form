<?php

namespace SGT\HTTP\Table\DataTable;

class Search
{

    public $start        = 0;
    public $limit        = 0;
    public $column       = -1;
    public $sort_order   = 'ASC';
    public $search_value = '';

    public function fill($request, $custom_search_fields = [])
    {

        $this->start        = $request->input('start');
        $this->limit        = $request->input('length');
        $this->search_value = $request->input('search.value');
        $this->column       = $request->input('order.0.column');
        $this->sort_order   = $request->input('order.0.dir');

        foreach ($custom_search_fields as $field)
        {
            $this->$field = $request->input($field);
        }
    }

}