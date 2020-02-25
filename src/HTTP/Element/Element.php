<?php
/**
 * An attribute is an html attribute.
 * A data field is something else which may be needed by the data type.
 */

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;
use SGT\Traits\Config;

abstract class Element
{

    use Config;

    public    $form       = null;
    protected $data       = [];
    protected $attributes = [];
    protected $classes    = [];

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

    public function hasError()
    {

        return $this->form->hasError($this->getName());
    }

    public function getName()
    {

        return $this->getData('name');

    }

    public function getData($name, $default_value = null)
    {

        return Arr::get($this->data, $name, $default_value);

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

    public function value($value)
    {

        $this->data('value', $value);

        return $this;

    }

    public function getDivID()
    {

        return $this->getId() . '_div';
    }

    public function getId()
    {

        return $this->getData('id', $this->getName());
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

                if (is_array($model))
                {
                    $value = Arr::get($model, $model_field);
                }
                else
                {
                    $value = $model->$model_field;
                }

            }
        }

        $name = $this->getName();

        $value = Form::getValueAttribute($name, $value);

        return $value;

    }

    public function getModelField()
    {

        return $this->getData('model_field', $this->getName());
    }

    public function attribute($name, $value)
    {

        $this->attributes[$name] = $value;

        return $this;

    }

    public function id($id)
    {

        $this->data('id', $id);

        return $this;
    }

    public function drawLabel()
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

        if ($tooltip)
        {

            if ($required == true)
            {
                $title = 'Required. ' . $tooltip;
            }
            else
            {
                $title = $tooltip;
            }

            $attributes['title']       = $title;
            $attributes['data-toggle'] = 'tooltip';
        }

        $label = Form::label($element_name, $label, $attributes);

        if ($tooltip)
        {
            $label .= " <i title=\"$tooltip\" data-toggle=\"tooltip\" class=\"fa fa-question-circle\"></i>";
        }

        return $label;

    }

    public function getLabel()
    {

        $label = Arr::get($this->data, 'label', $this->getName());

        $label = str_replace('_id', '', $label);

        $label = str_replace('_', ' ', $label);

        $label = ucwords($label);

        if ($this->getData('required') == true && !empty($label))
        {
            $label = '* ' . $label;
        }

        return $label;
    }

    public function getAttributes()
    {

        return $this->attributes;
    }

    public function name($name)
    {

        $this->data('name', $name);

        return $this;
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

    public function parseOptions($options)
    {


        foreach ($options as $option => $value)
        {
            switch ($option)
            {
                case 'required':
                    $this->required();
                    break;
                case 'tooltip':
                    $this->toolTip($value);
                    break;
                case 'label':
                    $this->label($value);
                    break;
                case 'list':
                    $this->options($value);
                    break;
                case 'options':
                case 'attributes':
                    $this->attributes($value);

                    if (Arr::get($value, 'multiple'))
                    {
                        $this->multiple();
                    }
                    break;
                case 'prepend':
                    $this->prepend($value);
                    break;
                case 'append':
                    $this->append($value);
                    break;
            }
        }
    }

    public function required($required = true)
    {

        $this->data('required', $required);

        return $this;
    }

    public function toolTip($text)
    {

        $this->data('tooltip', $text);

        return $this;

    }

    public function label($label)
    {

        $this->data('label', $label);

        return $this;
    }

    public function attributes(array $attributes)
    {

        $this->attributes += $attributes;

        return $this;

    }

}