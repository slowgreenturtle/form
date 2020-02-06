<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class Select extends Element
{

    protected $type    = 'input';
    protected $classes = [];

    public function __construct()
    {

        parent::__construct();

        $this->size(10);

    }

    public function size($size)
    {

        $this->attributes['size'] = $size;
    }

    public function options($data)
    {

        $this->data('options', $data);

        return $this;
    }

    public function getDivID()
    {

        return $this->getId() . '_div';
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

        $view_file = $this->getData('view_file', $element_view_path);

        $view_file .= '/' . $this->type;

        $view          = view($view_file);
        $view->element = $this;

        return $view->__toString();
    }

    public function multiple($state = true)
    {

        $this->data('multiple', $state);

        return $this;
    }

    public function element()
    {

        $element_name = $this->getName();

        if ($this->hasError())
        {
            $this->addClass('element', $this->configFrontEnd('element.input.css.error'));
        }

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = implode(" ", $this->getClass('element'));

        $options = $this->getData('options', []);

        $selected = $this->getData('value');

        $multiple = $this->getData('multiple');

        if ($multiple)
        {

            $attributes['multiple'] = $multiple;
            $attributes['name']     = $element_name . '[]';
        }

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        return Form::select($element_name, $options, $selected, $attributes);

    }

    public function getType()
    {

        return $this->type;
    }

    public function addClass($type, $class)
    {

        if (is_array($class))
        {

            $classes = Arr::get($this->classes, $type, []);

            foreach ($class as $item)
            {
                $classes[$item] = $item;
            }

            $this->classes[$type] = $classes;

        }
        else
        {
            $this->classes[$type][$class] = $class;
        }

    }

    public function getClass($type, $implode = false)
    {

        $classes = Arr::get($this->classes, $type, []);

        if ($implode == true)
        {
            return implode(' ', $classes);
        }

        return $classes;

    }
}