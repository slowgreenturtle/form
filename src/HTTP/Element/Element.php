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
    protected $attributes = [];
    protected $classes    = [];
    protected $data       = [];
    # show param must return for the object to show itself. Otherwise for html elements will simply return an empty string.
    protected $show_param = true;

    public function __construct()
    {

        $this->data('view_file', '');

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

        return $this;
    }

    public function attribute($name, $value)
    {

        $this->attributes[$name] = $value;

        return $this;

    }

    public function attributes(array $attributes)
    {

        $this->attributes += $attributes;

        return $this;

    }

    public function data($name, $value)
    {

        $this->data[$name] = $value;

        return $this;

    }

    abstract public function draw();

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

        $label = Form::label($element_name, $label, $attributes);

        if ($tooltip)
        {
            if ($required == true)
            {
                $tooltip = "Required.<br><div class='text-justify'>" . $tooltip . '</div>';
            }

            $label .= " <i title=\"$tooltip\" data-html=\"true\" data-toggle=\"tooltip\" class=\"fa fa-question-circle\"></i>";

        }

        return $label;

    }

    /**
     * Set the HTML element name
     *
     * @param $element_name
     *
     * @return $this
     */
    public function elementName($element_name)
    {

        $this->data('element_name', $element_name);

        return $this;
    }

    public function getAttributes()
    {

        return $this->attributes;
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

    public function getData($name, $default_value = null)
    {

        return Arr::get($this->data, $name, $default_value);

    }

    public function getDivID(): string
    {

        return $this->getId() . '_div';
    }

    /**
     * This the name of the element that will be used in the form.
     *
     * @return mixed
     */
    public function getElementName(): string
    {

        return $this->getData('element_name', $this->getName());
    }

    public function getId(): string
    {

        return $this->getData('id', $this->getName());
    }

    public function getLabel(): string
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

    public function getModelField()
    {

        return $this->getData('model_field', $this->getName());
    }

    /**
     * The internal name of the element
     *
     * @return string
     */
    public function getName(): string
    {

        return $this->getData('name');

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

    public function hasError()
    {

        return $this->form->hasError($this->getName());
    }

    /**
     * Set the HTML id of the element
     *
     * @param $id
     *
     * @return $this
     */
    public function id($id)
    {

        $this->data('id', $id);

        return $this;
    }

    /**
     * Set the HTML label of the element
     *
     * @param $label
     *
     * @return $this
     */
    public function label($label)
    {

        $this->data('label', $label);

        return $this;
    }

    public function name($name)
    {

        $this->data('name', $name);

        return $this;
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

    /**
     *
     */
    public function show($param)
    {

        $this->show_param = $param;

        return $this;

    }

    public function toolTip($text)
    {

        $this->data('tooltip', $text);

        return $this;

    }

    public function value($value)
    {

        $this->data('value', $value);

        return $this;

    }

}
