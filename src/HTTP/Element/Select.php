<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class Select extends Element
{

    protected $type      = 'select';
    protected $type_file = 'select';

    public function __construct()
    {

        parent::__construct();

        $this->multiple(false)->options([]);
        $this->addClass('div', $this->configFrontEnd('element.select.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.select.css.element'));

    }

    public function options($data)
    {

        $this->data('options', $data);

        return $this;
    }

    public function multiple($state = true)
    {

        $this->data('multiple', $state);

        return $this;
    }

    public function size($size)
    {

        $this->attribute('size', $size);

        return $this;
    }

    public function draw()
    {

        if ($this->hasError())
        {
            $error_class = $this->configFrontEnd('element.css.error');
            $this->addClass('element', $error_class);
            $this->addClass('div', $error_class);

        }

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

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        $options  = $this->getData('options');
        $selected = $this->getValue();

        $multiple = $this->getData('multiple');

        if ($multiple)
        {

            $attributes['multiple'] = $multiple;
            $attributes['name']     = $element_name . '[]';
            # in case size is not set, we set it here.
            $attributes['size'] = Arr::get($attributes, 'size', 10);
        }

        return Form::select($element_name, $options, $selected, $attributes);

    }

}
