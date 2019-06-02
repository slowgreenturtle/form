<?php

namespace SGT\Element;

use Form;

class Input extends Element
{

    protected $_type = 'input';

    public function draw()
    {

        $data = $this->viewDataDefault($element);

        $name = array_get($element, 'name');

        $class = array_get($element, 'class');

        $classes = ['form-control'];

        if ($this->hasError($name))
        {
            $classes[] = 'form-control-danger';
        }

        if ($class)
        {
            $classes = array_merge($classes, $class);
        };

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes),
        ];

        $attributes += array_get($element, 'options', []);

        $data['form_element'] = Form::input($this->_type, $name, $this->getValue($name), $attributes);

        $view_file = $this->getViewFile();

        return view($view_file, ['element' => $this])->__toString();

    }

    public function getViewFile()
    {

        $element_view_path = config('sgtform.element.view.path');

        $view_file = $this->view_file;

        $view_file = $view_file == '' ? $element_view_path : $view_file;

        $view_file .= '/' . $this->type;

        return $view_file;
    }

}