<?php

namespace SGT\HTTP\Element;

use Form;

class Hidden extends Element
{

    protected $type = 'hidden';

    public function draw()
    {

        $element_name = $this->getElementName();
        $value = $this->getValue();

        $attributes       = $this->getAttributes();
        $attributes['id'] = $this->getId();

        return Form::hidden($element_name, $value, $attributes);
    }

}