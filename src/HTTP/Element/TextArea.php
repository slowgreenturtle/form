<?php

namespace SGT\HTTP\Element;

use Illuminate\Support\Arr;
use Form;
class TextArea extends Input
{

    protected $type = 'textarea';

    public function drawElement()
    {

        $element_name = $this->getName();

        if ($this->hasError())
        {
            $this->addClass('element', $this->configFrontEnd('element.css.error'));
        }

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = implode(" ", $this->getClass('element'));

        return Form::textarea($element_name, $this->getValue(), $attributes);

    }
}