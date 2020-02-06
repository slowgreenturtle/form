<?php

/**
 * All set methods are simply the name.
 * All get methods are getAttribute or getLabel.
 *
 */

namespace SGT\HTTP;

use Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use SGT\HTTP\Element\Color;
use SGT\HTTP\Element\Hidden;
use SGT\HTTP\Element\Input;
use SGT\HTTP\Element\Select;
use SGT\HTTP\Element\Submit;
use SGT\HTTP\Element\TextArea;
use SGT\Traits\Config;

abstract class SGTForm2
{

    use Config;

    public $errors = null;

    /**
     * @var array The attributes attached to the form. Method, etc.
     *
     */
    public $attributes = [];

    protected $model = null;

    /**
     * @var array The fields which are created for the form
     */
    protected $elements = [];

    protected $scripts = [];

    protected $view = null;

    /** @var string $view_file The Laravel view file */
    protected $view_file         = '';
    protected $element_view_path = '';

    public function __construct($model = null)
    {

        $this->attribute('name', snake_case(class_basename($this)));
        $this->attribute('method', 'POST');
        $this->attribute('id', $this->getAttribute('name'));

        if ($this->view_file)
        {
            $this->view = view($this->view_file);
        }

        $this->model = $model;

        Form::setModel($model);

        $this->element_view_path = $this->configFrontEnd('element.view.path');

        $this->build();

        $this->add('return_url', 'hidden');

        $this->setup();

    }

    public function attribute($name, $value)
    {

        $this->attributes[$name] = $value;

        return $this;

    }

    public function getAttribute($name, $default_value = null)
    {

        return Arr::get($this->attributes, $name, $default_value);
    }

    abstract protected function build();

    public function add($name, $type)
    {

        switch ($type)
        {
            case 'text':
                $element = new Input();
                break;
            case 'submit':
                $element = new Submit();
                break;
            case 'hidden':
                $element = new Hidden();
                break;
            case 'select':
                $element = new Select();
                break;
            case 'color':
                $element = new Color();
                break;
            case 'textarea':
                $element = new TextArea();
                break;
            default:
                return null;
        }

        $this->elements[$name] = $element;

        $element->name($name);
        $element->form = $this;

        return $element;
    }

    protected function setup()
    {

    }

    public function getModel()
    {

        return $this->model;
    }

    public function element($name)
    {

        return Arr::get($this->elements, $name);
    }

    public function submitButton($value = 'Submit', $name = null)
    {

        if ($name == null)
        {
            $name = 'submit_' . Str::slug($value);
        }

        $element = $this->add('submit', $name);

        $element['label'] = '&nbsp;';
        $this->attribute('value', $value);

        return $element;

    }

    public function __toString()
    {

        if ($this->view)
        {
            $this->view->form = $this;

            return $this->view->__toString();
        }

        return '';
    }

    /**
     * Returns a list of field
     */

    public function elementNames()
    {

        return array_keys($this->elements);
    }

    public function open($errors)
    {

        $this->errors = $errors;

        return Form::open($this->attributes);
    }

    public function close()
    {

        return Form::close();
    }

    public function elementExists($name)
    {

        return isset($this->elements[$name]);
    }

    public function __get($method)
    {

        $element = Arr::get($this->elements, $method);

        if ($element == null)
        {
            return '';
        }

        return $element->draw();

    }

    public function url($element)
    {

        $element['type'] = 'url';

        return $this->input($element);

    }

    public function input($element)
    {

        $data = $this->viewDataDefault($element);

        $name = Arr::get($element, 'name');

        $type                 = Arr::get($element, 'type', 'text');
        $data['append_text']  = Arr::get($element, 'append');
        $data['prepend_text'] = Arr::get($element, 'prepend');
        $data['help']         = Arr::get($element, 'help');

        $class = Arr::get($element, 'class');

        $classes = ['form-control'];

        if ($this->hasError($name))
        {
            $classes[] = $this->configFrontEnd('element.input.css.error');

        }

        if ($class)
        {
            $classes = array_merge($classes, $class);
        }

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes),
        ];

        $attributes += Arr::get($element, 'options', []);

        $data['form_element'] = Form::input($type, $name, $this->getValue($name), $attributes);

        return $this->elementView($data, $element);

    }

    protected function viewDataDefault($element)
    {

        $data                = [];
        $name                = Arr::get($element, 'name');
        $data['div_name']    = $name . '_div';
        $data['div_classes'] = $this->makeDivClasses($name);
        $data['label']       = $this->label($element);

        return $data;
    }

    protected function makeDivClasses($name)
    {

        $div_classes = ['form-group'];

        if ($this->hasError($name))
        {
            $div_classes[] = 'has-error';
        }

        return implode(' ', $div_classes);

    }

    public function hasError($element)
    {

        if ($this->errors)
        {
            return $this->errors->default->has($element);
        }

        return false;

    }

    public function label($element)
    {

        $element_name = Arr::get($element, 'name');

        $label_text = Arr::get($element, 'label', $element_name);
        $required   = Arr::get($element, 'required', false);

        $label_text = str_replace('_id', '', $label_text);

        $label_text = str_replace('_', ' ', $label_text);

        $label_text = ucwords($label_text);

        $attributes = ['class' => 'control-label'];

        if (empty($label_text))
        {
            return '';
        }

        $tooltip = Arr::get($element, 'tooltip', Arr::get($this->tooltips, $element_name));

        if ($required == true)
        {
            $label_text = '* ' . $label_text;

            if (strlen($tooltip) > 0)
            {
                $tooltip = 'Required. ' . $tooltip;
            }
        }

        $label = Form::label($element_name, $label_text, $attributes);

        if ($tooltip)
        {
            $label .= " <i title=\"$tooltip\" data-toggle=\"tooltip\" class=\"fa fa-question-circle\"></i>";
        }

        return $label;

    }

    public function getValue($name)
    {

        $value = $this->getParam($name);

        if ($value === null && is_object($this->model))
        {
            $value = $this->model->$name;
        }

        $value = Form::getValueAttribute($name, $value);

        return $value;

    }

    protected function elementView($data, $element)
    {

        $data['element_id']   = Arr::get($element, 'name');
        $data['element_name'] = Arr::get($element, 'name');

        $view_form = Arr::get($element, 'view', $this->element_view_path);
        $type      = Arr::get($element, 'type');

        $view_form .= '/' . $type;

        return view($view_form, $data)->__toString();

    }

    public function date($element)
    {

        $element['class'] = Arr::get($element, 'class', []) + ['date'];
        $element['type']  = 'date';

        return $this->input($element);

    }

    public function date_time($element)
    {

        $element['class'] = Arr::get($element, 'class', []) + ['datetime'];
        $element['type']  = 'text';

        return $this->input($element);

    }

    public function time($element)
    {

        $element['class'] = Arr::get($element, 'class', []) + ['time'];
        $element['type']  = 'text';

        return $this->input($element);

    }

    public function text($element)
    {

        $element['type'] = 'text';

        return $this->input($element);
    }

    public function password($element)
    {

        $element['type'] = 'password';

        return $this->input($element);
    }

    public function email($element)
    {

        $element['type'] = 'email';

        return $this->input($element);
    }

    public function number($element)
    {

        $element['type'] = 'number';

        return $this->input($element);
    }

    public function checkbox($element)
    {

        $name = Arr::get($element, 'name');

        $check = Arr::get($element, 'check');
        $value = Arr::get($element, 'value', 1);

        $div_name = $name . '_div';

        $html = '<div class="form-group" id="' . $div_name . '">';

        $class = Arr::get($element, 'class');

        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        }

        $html .= $this->label($element);

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $options = Arr::get($element, 'options');

        if (is_array($options))
        {
            $attributes += $options;
        }

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        $html .= Form::checkbox($name, $value, $check, $attributes);

        $html .= '</div>';

        return $html;

    }

    public function date_range($element)
    {

        return 'date range here';
    }

    /**
     * A select tag form is a multiple select form which displays the results as tags in the field location.
     * The results are sent to the server as an array.
     *
     * @param $element
     */
    public function select_tag($element)
    {

        $name     = Arr::get($element, 'name');
        $list     = Arr::get($element, 'list', []);
        $selected = $this->getValue($name);

        $data = [];

        $data['div_name']    = $name . '_div';
        $data['div_classes'] = $this->makeDivClasses($name);

        $data['label'] = $this->label($element);

        $class   = Arr::get($element, 'class');
        $classes = ['form-control', 'select2-multiple'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        }

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $add_atributes = Arr::get($element, 'attributes', []);

        $attributes                += $add_atributes;
        $attributes['name']        = $name . '[]';
        $attributes['size']        = Arr::get($attributes, 'size', 10);
        $attributes['multiple']    = 'multiple';
        $attributes['aria-hidden'] = true;

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        $data['form_element'] = Form::select($name, $list, $selected, $attributes);

        return $this->elementView($data, $element);

    }

    public function select($element)
    {

        $name     = Arr::get($element, 'name');
        $list     = Arr::get($element, 'list', []);
        $selected = $this->getValue($name);

        $data = [];

        $data['div_name']    = $name . '_div';
        $data['div_classes'] = $this->makeDivClasses($name);

        $data['label'] = $this->label($element);

        $class   = Arr::get($element, 'class');
        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        }

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $add_atributes = Arr::get($element, 'attributes', []);

        $attributes += $add_atributes;

        $multiselect = Arr::get($attributes, 'multiple');

        if ($multiselect)
        {
            $attributes['name'] = $name . '[]';
            $attributes['size'] = Arr::get($attributes, 'size', 10);
        }

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        $data['form_element'] = Form::select($name, $list, $selected, $attributes);

        return $this->elementView($data, $element);

    }

    public function scripts()
    {

        $html = implode(' ', $this->scripts);

        return $html;
    }

    public function color($element)
    {

        $element['type'] = 'color';

        return $this->input($element);
    }

    public function textarea($element)
    {

        $data = $this->viewDataDefault($element);

        $name  = Arr::get($element, 'name');
        $class = Arr::get($element, 'class');

        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        }

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $attributes += Arr::get($element, 'options', []);

        $data['form_element'] = Form::textarea($name, $this->getValue($name), $attributes);

        return $this->elementView($data, $element);

    }
}
