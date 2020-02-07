<?php

namespace SGT\HTTP\Element;

class DateTime extends Input
{

    protected $type = 'text';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('element', 'datetime');
    }

}