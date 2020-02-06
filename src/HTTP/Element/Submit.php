<?php

namespace SGT\HTTP\Element;

use Form;
use SGT\Traits\Config;

class Submit extends Element
{

    use Config;

    public function draw()
    {

        $data = $this->viewDataDefault($element);

        $name = Arr::get($element, 'name');

        $class = Arr::get($element, 'class');

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

        $attributes += Arr::get($element, 'options', []);

        $data['form_element'] = Form::input($this->_type, $name, $this->getValue($name), $attributes);

        $view_file = $this->getViewFile();

        return view($view_file, ['element' => $this])->__toString();

    }

    public function getViewFile()
    {

        $element_view_path = $this->configFrontEnd('element.view.path');

        $view_file = $this->view_file;

        $view_file = $view_file == '' ? $element_view_path : $view_file;

        $view_file .= '/' . $this->type;

        return $view_file;
    }

}