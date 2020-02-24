<?php

namespace SGT\HTTP\Element;

use Form;

class Checkbox extends Input
{

    protected $type      = 'checkbox';

    public function __construct()
    {

        parent::__construct();
        $this->value(1);

    }

    public function check($check = true)
    {

        $this->data('checked', $check);
    }

    public function drawElement()
    {

        $element_name = $this->getName();
        $attributes   = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        $checked = $this->getData('checked');
        $value   = $this->getValue();

        return Form::checkbox($element_name, $value, $checked, $attributes);
    }
}