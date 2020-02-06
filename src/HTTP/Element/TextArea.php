<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;

class TextArea extends Element
{

    protected $type    = 'input';
    protected $classes = [];

    public function __construct()
    {

        parent::__construct();

        $this->addClass('div', $this->configFrontEnd('element.input.css.div'));

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

    public function element()
    {

        $element_name = $this->getName();
        $type         = $this->getType();

        $this->addClass('element', 'form-control');

        if ($this->hasError())
        {
            $classes[] = $this->configFrontEnd('element.input.css.error');
        }

        $attributes = $this->getAttributes();

        $attributes['id']    = $this->getId();
        $attributes['class'] = implode(' ', $this->getClass('element'));

        return Form::input($type, $element_name, $this->getValue(), $attributes);
    }

    public function getType()
    {

        return $this->type;
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