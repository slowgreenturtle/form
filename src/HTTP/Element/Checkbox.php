<?php

namespace SGT\HTTP\Element;

use Form;

class Checkbox extends Element
{

    protected $type      = 'checkbox';
    protected $type_file = 'checkbox';

    public function __construct()
    {

        $this->value(1);

        $this->addClass('div', $this->configFrontEnd('element.checkbox.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.checkbox.css.element'));

    }

    public function check($check = true)
    {

        $this->data('checked', $check);
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
        $attributes   = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = $this->getClass('element', true);

        $checked = $this->getData('checked');
        $value   = $this->getValue();

        return Form::checkbox($element_name, $value, $checked, $attributes);
    }
}
