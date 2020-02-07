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
use SGT\HTTP\Element\Checkbox;
use SGT\HTTP\Element\Color;
use SGT\HTTP\Element\Date;
use SGT\HTTP\Element\DateRange;
use SGT\HTTP\Element\DateTime;
use SGT\HTTP\Element\Email;
use SGT\HTTP\Element\Hidden;
use SGT\HTTP\Element\Input;
use SGT\HTTP\Element\Number;
use SGT\HTTP\Element\Password;
use SGT\HTTP\Element\Select;
use SGT\HTTP\Element\Submit;
use SGT\HTTP\Element\TextArea;
use SGT\HTTP\Element\Time;
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
            case 'email':
                $element = new Email();
                break;
            case 'number':
                $element = new Number();
                break;
            case 'password':
                $element = new Password();
                break;
            case 'date':
                $element = new Date();
                break;
            case 'datetime':
                $element = new DateTime();
                break;
            case 'time':
                $element = new Time();
                break;
            case 'daterange':
                $element = new DateRange();
                break;
            case 'checkbox':
                $element = new Checkbox();
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

        $element = $this->add($name, 'submit');

        $element->label('&nbsp;');
        $element->value($value);

        return $element->draw();

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

    public function hasError($element)
    {

        if ($this->errors)
        {
            return $this->errors->default->has($element);
        }

        return false;

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

    public function scripts()
    {

        $html = implode(' ', $this->scripts);

        return $html;
    }
}
