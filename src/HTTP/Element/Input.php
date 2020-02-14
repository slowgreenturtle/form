<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class Input extends Element
{

    protected $type      = 'input';
    protected $type_file = 'input';

    public function __construct()
    {

        parent::__construct();

        $this->addClass('div', $this->configFrontEnd('element.input.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.input.css.element'));
    }

    public function append($text)
    {

        $this->data('append', $text);

        return $this;
    }

    public function prepend($text)
    {

        $this->data('prepend', $text);

        return $this;
    }

    public function draw()
    {

        $element_view_path = $this->configFrontEnd('element.view.path');

        $view_file = $this->getData('view_file');

        $view_file = empty($view_file) ? $element_view_path : $view_file;

        $view_file .= '/' . $this->type_file;

        $view          = view($view_file);
        $view->element = $this;

        return $view->__toString();
    }

    public function drawElement()
    {

        $element_name = $this->getName();
        $type         = $this->getType();

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        return Form::input($type, $element_name, $this->getValue(), $attributes);
    }

    public function getType()
    {

        return $this->type;
    }

}