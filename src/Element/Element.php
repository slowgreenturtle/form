<?php

namespace SGT\Element;

use Form;

abstract class Element
{

    public $attributes = [];

    public function __construct($attributes)
    {

        $this->attributes = [];
    }
    abstract public function draw();

    protected function viewDataDefault($element)
    {

        $data                = [];
        $name                = array_get($element, 'name');
        $data['div_name']    = $name . '_div';
        $data['div_classes'] = $this->makeDivClasses($name);
        $data['label']       = $this->label($element);

        return $data;
    }

    protected function makeDivClasses($name)
    {

        $div_classes = ['form-group'];

        if ($this->hasError($name))
        {
            $div_classes[] = 'has-error';
        }

        return implode(' ', $div_classes);

    }

    public function hasError($field)
    {

        if ($this->errors)
        {
            return $this->errors->default->has($field);
        }

        return false;

    }
}