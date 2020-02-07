<?php

namespace SGT\HTTP\Element;

class DateRange extends Input
{

    protected $type = 'text';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('element', 'date_range');
    }

}