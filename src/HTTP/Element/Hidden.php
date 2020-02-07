<?php

namespace SGT\HTTP\Element;

use Form;

class Hidden extends Element
{

    public function draw()
    {

        $name  = $this->getName();
        $value = $this->getValue();

        $attributes       = $this->getAttributes();
        $attributes['id'] = $this->getId();

        return Form::hidden($name, $value, $attributes);
    }
}