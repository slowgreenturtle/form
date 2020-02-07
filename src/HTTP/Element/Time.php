<?php

namespace SGT\HTTP\Element;

class Time extends Input
{

    protected $type = 'text';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('element', 'time');
    }

}