<?php

namespace SGT\HTTP\Element;

class Date extends Input
{

    protected $type = 'date';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('element', 'date');
    }

}