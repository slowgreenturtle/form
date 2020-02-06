<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class Select extends Element
{

    protected $type = 'select';

    public function __construct()
    {

        parent::__construct();

        $this->size(10)->multiple(false)->options([])->value('');
        $this->addClass('div', $this->configFrontEnd('element.select.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.select.css.element'));

    }

    public function size($size)
    {

        $this->attributes['size'] = $size;
    }

    public function multiple($state = true)
    {

        $this->data('multiple', $state);

        return $this;
    }

    public function options($data)
    {

        $this->data('options', $data);

        return $this;
    }

    public function draw()
    {

        $element_view_path = $this->configFrontEnd('element.view.path');

        $view_file = $this->getData('view_file');

        $view_file = empty($view_file) ? $element_view_path : $view_file;

        $view_file .= '/' . $this->type;

        $view          = view($view_file);
        $view->element = $this;

        return $view->__toString();
    }

    public function drawElement()
    {

        $element_name = $this->getName();

        if ($this->hasError())
        {
            $this->addClass('element', $this->configFrontEnd('element.css.error'));
        }

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        $options  = $this->getData('options');
        $selected = $this->getData('value');

        $multiple = $this->getData('multiple');

        if ($multiple)
        {

            $attributes['multiple'] = $multiple;
            $attributes['name']     = $element_name . '[]';
        }

        return Form::select($element_name, $options, $selected, $attributes);

    }

}