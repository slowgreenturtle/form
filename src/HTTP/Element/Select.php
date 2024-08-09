<?php

namespace SGT\HTTP\Element;

use Form;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends Element
{

    protected $type      = 'select';
    protected $type_file = 'select';

    public function __construct()
    {

        parent::__construct();

        $this->multiple(false)->options([]);
        $this->addClass('div', $this->configFrontEnd('element.select.css.div'));
        $this->addClass('element', $this->configFrontEnd('element.select.css.element'));

    }

    public function draw()
    {

        if ($this->hasError())
        {
            $error_class = $this->configFrontEnd('element.css.error');
            $this->addClass('element', $error_class);
            $this->addClass('div', $error_class);

        }

        $element_view_path = $this->configFrontEnd('element.view.path');

        $view_file = $this->getData('view_file');

        $view_file = empty($view_file) ? $element_view_path : $view_file;

        $view_file .= '/' . $this->type_file;

        $view          = view($view_file);
        $view->element = $this;

        return $view->__toString();
    }

    public function drawElement()
    {

        $options = $this->getData('options');

        $count = 0;

        if (is_array($options))
        {
            $count = count($options);
        }
        elseif ($options instanceof Collection)
        {
            $count = $options->count();
        }

        if ($count > 9)
        {
            $this->attribute('data-live-search', 'true');
            $this->attribute('data-actions-box', 'true');
        }

        $element_name = $this->getElementName();

        $select_attributes = $this->getAttributes();

        $select_attributes['id']    = $this->getId();
        $select_attributes['class'] = $this->getClass('element', true);

        $selected = $this->getValue();

        $multiple = $this->getData('multiple');

        if ($multiple)
        {

            $select_attributes['multiple'] = $multiple;
            $select_attributes['name']     = $element_name . '[]';
            # in case size is not set, we set it here.
            $select_attributes['size'] = Arr::get($select_attributes, 'size', 10);
        }

        $options_attributes      = $this->getData('options_attributes') ?? [];
        $option_group_attributes = $this->getData('option_group_attributes') ?? [];

        if ($option_group_attributes instanceof Collection)
        {
            $option_group_attributes = $option_group_attributes->toArray();
        }

        return Form::select($element_name, $options, $selected, $select_attributes, $options_attributes, $option_group_attributes);

    }

    public function multiple($state = true)
    {

        $this->data('multiple', $state);

        return $this;
    }

    /**
     * HTML attributes to attach to each option. Must be mapped to the value of the particular option
     *
     * @param $data
     *
     * @return $this
     */
    public function optionAttributes($data)
    {

        $this->data('options_attributes', $data);

        return $this;
    }

    /**
     * The key of the array is the option value, the value is the option text.
     *
     */
    public function optionGroup($data)
    {

        $this->data('option_group_attributes', $data);

        return $this;
    }

    public function options($data)
    {

        $this->data('options', $data);

        return $this;
    }

    public function size($size)
    {

        $this->attribute('size', $size);

        return $this;
    }

}
