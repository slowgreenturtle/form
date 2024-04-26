<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class TextArea extends Input
{

    protected $type = 'textarea';

    public function drawElement()
    {

        $element_name = $this->getElementName();
        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = implode(" ", $this->getClass('element'));

        return Form::textarea($element_name, $this->getValue(), $attributes);

    }

}