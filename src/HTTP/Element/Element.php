<?php
/**
 *
 * An attribute is an html attribute.
 * A data field is something else which may be needed by the data type.
 *
 *
 */

namespace SGT\HTTP\Element;

use Form;
use SGT\Traits\Config;

abstract class Element
{

    use Config;

    public    $form       = null;
    protected $data       = [];
    protected $attributes = [];

    public function __construct()
    {

        $this->data('view_file', '');

    }

    public function data($name, $value)
    {

        $this->data[$name] = $value;

        return $this;

    }

    abstract public function draw();

    public function getId()
    {

        return $this->getData('id', $this->getName());
    }

    public function getData($name, $default_value = null)
    {

        return Arr::get($this->data, $name, $default_value);

    }

    public function getName()
    {

        return $this->getData('name');

    }

    public function value($value)
    {

        $this->data('value', $value);

    }

    public function getValue()
    {

        $value = $this->getData('value');

        if ($value === null)
        {
            $model = $this->form->getModel();

            if ($model)
            {
                $model_field = $this->getModelField();
                $value       = $model->$model_field;
            }
        }

        $name = $this->getName();

        $value = Form::getValueAttribute($name, $value);

        return $value;

    }

    public function getModelField()
    {

        $model_field = $this->getData('model_field', $this->getName());

        if ($model_field == null)
        {
        }

        return $model_field;
    }

    public function required($required = true)
    {

        $this->data('required', $required);

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

    public function hasError()
    {

        return $this->form->hasError($this->getName());
    }

    public function attribute($name, $value)
    {

        $this->attributes[$name] = $value;

    }

    public function label($label)
    {

        $this->data('label', $label);

        return $this;
    }

    protected function viewDataDefault($element)
    {

        $data             = [];
        $name             = $this->getName();
        $data['div_name'] = $name . '_div';

        return $data;
    }

    protected function drawLabel()
    {

        $label = $this->getLabel();

        if (empty($label))
        {
            return '';
        }

        $element_name = 'label_' . $this->getName();

        $required = $this->getData('required');

        $attributes = $this->getAttributes();

        $attributes['class'] = 'control-label';

        $tooltip = $this->getData('tooltip');

        if ($required == true)
        {
            $tooltip = 'Required. ' . $tooltip;
        }

        if ($tooltip)
        {
            $attributes['title']       = $tooltip;
            $attributes['data-toggle'] = 'tooltip';
        }

        $label = Form::label($element_name, $label, $attributes);

        return $label;

    }

    public function getLabel()
    {

        $label = Arr::get($this->data, 'label', $this->name());

        $label = str_replace('_id', '', $label);

        if ($this->getData('required') == true && !empty($label))
        {
            $label = '* ' . $label;
        }

        return $label;

    }

    public function name($name)
    {

        $this->data('name', $name);
    }

    public function getAttributes()
    {

        return $this->attributes;
    }
}