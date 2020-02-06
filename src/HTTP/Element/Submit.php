<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class Submit extends Input
{

    protected $type = 'submit';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('div', $this->configFrontEnd('element.button.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.button.css.element'));
    }

}