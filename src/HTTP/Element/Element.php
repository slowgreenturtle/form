<?php

namespace SGT\HTTP\Element;

use Form;

abstract class Element
{

    public $attributes = [];

    public function __construct($attributes)
    {

        $this->attributes['view_file'] = '';

        $this->attributes = array_merge($this->attributes, $attributes);

    }

    abstract public function draw();

    public function required($required)
    {

        $this->attributes['required'] = $required;

        return $this;
    }

    public function getDivClassesAttribute()
    {

        $div_classes = ['form-group'];

        if ($this->hasError($this->name))
        {
            $div_classes[] = 'has-error';
        }

        return implode(' ', $div_classes);

    }

    public function hasError($field)
    {

        if ($this->errors)
        {
            return $this->errors->default->has($field);
        }

        return false;

    }

    public function attributes($attributes)
    {

        array_merge($this->attributes, $attributes);

        return $this;

    }

    public function getLabelAttribute()
    {

        $label = array_get($this->attributes, 'label', $this->name);
        $label = str_replace('_id', '', $label);

        if ($this->required == true && !empty($label))
        {
            $label = '* ' . $label;
        }

        return $label;

    }

    public function label($label)
    {

        $this->attributes['label'] = $label;

        return $this;
    }

    protected function viewDataDefault($element)
    {

        $data             = [];
        $name             = array_get($element, 'name');
        $data['div_name'] = $name . '_div';

        return $data;
    }

    protected function buildLabel()
    {

        $label_text = $this->label;

        if (empty($label_text))
        {
            return '';
        }

        $element_name = $this->name;

        $required = $this->required;

        $attributes = ['class' => 'control-label'];

        $tooltip = array_get($element_name, 'tooltip', array_get($this->tooltips, $element_name));

        if ($required == true)
        {
            $tooltip = 'Required. ' . $tooltip;
        }

        if ($tooltip)
        {
            $attributes['title']       = $tooltip;
            $attributes['data-toggle'] = 'tooltip';
        }

        $label = Form::label($element_name, $label_text, $attributes);

        return $label;

    }
}