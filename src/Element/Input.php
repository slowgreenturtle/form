<?php

namespace SGT\Element;

use Form;

class Input extends Element
{

    public $attributes = [];

    public function __construct($attributes)
    {

        $this->setDefaults();

        $this->attributes = $attributes;
    }

    public function setDefaults()
    {


    }

    public function draw()
    {

        $data = $this->viewDataDefault($element);

        $name = array_get($element, 'name');

        $type                 = array_get($element, 'type', 'text');
        $data['append_text']  = array_get($element, 'append');
        $data['prepend_text'] = array_get($element, 'prepend');
        $data['help']         = array_get($element, 'help');

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

        $data['form_element'] = Form::input($type, $name, $this->getValue($name), $attributes);

        $element_view_path = config('sgtform.element.view.path');


        $view_form = array_get($element, 'view', $element_view_path);
        $type      = array_get($element, 'type');

        $view_form .= '/' . $type;

        return view($view_form, $data)->d__toString();

    }

}