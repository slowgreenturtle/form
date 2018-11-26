<?php

namespace SGT;

use Form;

abstract class SGTForm
{

    public $form_url = '';
    public $errors   = null;
    public $method   = 'POST';

    public $model        = null;
    public $form_options = [];

    protected $fields   = [];
    protected $params   = [];
    protected $tooltips = [];

    protected $scripts = [];

    protected $view = null;

    /** @var string $view_file The Laravel view file */
    protected $view_file         = '';
    protected $element_view_path = '';

    public function __construct($model = null)
    {

        if ($this->view_file)
        {
            $this->view = view($this->view_file);
        }

        $this->model = $model;

        Form::setModel($model);

        $this->element_view_path = config('sgtform.element.view.path');

        $this->build();

        $this->setup();

    }

    abstract protected function build();

    protected function setup()
    {

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

    public function field_names()
    {

        return array_keys($this->fields);
    }

    public function setTooltips($tooltips)
    {

        $this->tooltips = $tooltips;
    }

    public function open($errors, array $options = [])
    {

        $this->errors = $errors;

        $form_options = [
            'method' => $this->method];

        if ($this->form_url != '')
        {
            $form_options['url'] = $this->form_url;
        }

        $options += $this->form_options;
        $options += $form_options;

        return Form::open($options);
    }

    public function close()
    {

        return Form::close();
    }

    public function add($name, $type, $attributes = [])
    {

        $this->fields[$name] = array_merge($attributes, ['type' => $type, 'name' => $name]);
    }

    public function field_exists($name)
    {

        return isset($this->fields[$name]);
    }

    public function field_set($name, $attributes)
    {

        $this->fields[$name] = $attributes;

    }

    public function field_update($name, $attribute, $value)
    {

        $this->fields[$name][$attribute] = $value;
    }

    public function __get($method)
    {

        $field = array_get($this->fields, $method);

        if ($field == null)
        {
            return $method;
        }

        $type = array_get($field, 'type');

        return $this->$type($field);

    }

    public function file($element)
    {

        $element['type'] = 'file';

        return $this->input($element);
    }

    public function input($element)
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
            $classes[] = Config('sgtform.element.input.css.error');

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

        return $this->elementView($data, $element);

    }

    protected function viewDataDefault($element)
    {

        $data                = [];
        $name                = array_get($element, 'name');
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

    public function hasError($field)
    {

        if ($this->errors)
        {
            return $this->errors->default->has($field);
        }

        return false;

    }

    public function label($element)
    {

        $element_name = array_get($element, 'name');

        $label_text = array_get($element, 'label', $element_name);
        $required   = array_get($element, 'required', false);

        $label_text = str_replace('_id', '', $label_text);

        $label_text = str_replace('_', ' ', $label_text);

        $label_text = ucwords($label_text);

        $attributes = ['class' => 'control-label'];

        if (empty($label_text))
        {
            return '';
        }

        $tooltip = array_get($element, 'tooltip', array_get($this->tooltips, $element_name));

        if ($required == true)
        {
            $label_text = '* ' . $label_text;

            if(strlen($tooltip) > 0)
            {
                $tooltip    = 'Required. ' . $tooltip;
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

    public function getParam($name, $default = null)
    {

        return array_get($this->params, $name, $default);
    }

    protected function elementView($data, $element)
    {

        $data['element_id']   = array_get($element, 'name');
        $data['element_name'] = array_get($element, 'name');

        $view_form = array_get($element, 'view', $this->element_view_path);
        $type      = array_get($element, 'type');

        $view_form .= '/' . $type;

        return view($view_form, $data)->__toString();

    }

    public function date($element)
    {

        $element['class'] = array_get($element, 'class', []) + ['date'];
        $element['type']  = 'text';

        return $this->input($element);

    }

    public function date_time($element)
    {

        $element['class'] = array_get($element, 'class', []) + ['datetime'];
        $element['type']  = 'text';

        return $this->input($element);

    }

    public function time($element)
    {

        $element['class'] = array_get($element, 'class', []) + ['time'];
        $element['type']  = 'text';

        return $this->input($element);

    }

    public function addParams($params)
    {

        $this->params += $params;
    }

    public function setParam($name, $value = null)
    {

        $this->params[$name] = $value;
    }

    public function setAttribute($name, $attribute, $value = null)
    {

        $this->params[$name][$attribute] = $value;
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

    public function hidden($element)
    {

        $name          = array_get($element, 'name');
        $value         = $this->getValue($name);
        $element['id'] = $name;

        return Form::hidden($name, $value, $element);
    }

    public function checkbox($element)
    {

        $name = array_get($element, 'name');

        $check = array_get($element, 'check');
        $value = array_get($element, 'value', 1);

        $div_name = $name . '_div';

        $html = '<div class="form-group" id="' . $div_name . '">';

        $class = array_get($element, 'class');

        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        };

        $html .= $this->label($element);

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

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

        $name = array_get($element, 'name');
        $list = array_get($element, 'list', []);
        $url  = array_get($element, 'url');

        $selected = $this->getValue($name);

        $div_name = $name . '_div';

        $html = '<div class="form-group" id="' . $div_name . '">';

        $class = array_get($element, 'class');

        $classes = [
            'form-control',
            'select2-multiple',
        ];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        };

        $html .= $this->label($element);

        $attributes = [
            'id'          => $name,
            'name'        => $name . '[]',
            'class'       => implode(' ', $classes),
            'multiple'    => 'multiple',
            'aria-hidden' => true
        ];

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        $html .= Form::select($name, $list, $selected, $attributes);

        $html .= '</div>';

        $data = [
            'element_name' => $name
        ];

        if ($url != null)
        {
            $data['url'] = $url;
        }

        $this->scripts[] = view('form.element.select_tag', $data);

        return $html;

    }

    public function select($element)
    {

        $name     = array_get($element, 'name');
        $list     = array_get($element, 'list', []);
        $selected = $this->getValue($name);

        $data = [];

        $data['div_name']    = $name . '_div';
        $data['div_classes'] = $this->makeDivClasses($name);

        $data['label'] = $this->label($element);

        $class   = array_get($element, 'class');
        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        };

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $add_attributes = array_get($element, 'attributes', []);
        $attributes     += $add_attributes;

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

        array_set($element, 'type', 'color');

        return $this->input($element);
    }

    public function textarea($element)
    {

        $data = $this->viewDataDefault($element);

        $name  = array_get($element, 'name');
        $class = array_get($element, 'class');

        $classes = ['form-control'];

        if ($class)
        {
            $classes = array_merge($classes, $class);
        };

        $attributes = [
            'id'    => $name,
            'name'  => $name,
            'class' => implode(' ', $classes)];

        $attributes += array_get($element, 'options', []);

        if ($this->model)
        {
            Form::setModel($this->model);
        }

        $data['form_element'] = Form::textarea($name, null, $attributes);

        return $this->elementView($data, $element);

    }
}