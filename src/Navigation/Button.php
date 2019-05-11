<?php

namespace SGT\Navigation;

class Button extends Item
{

    public $type = 'button';

    protected $config_colors = 'sgtform.element.button.colors';
    protected $config_sizes  = 'sgtform.element.button.sizes';

    public static function create($label = '')
    {

        return new Button($label);
    }

    public function display()
    {

        if (!$this->hasPermission())
        {
            return '';
        }

        $class = $this->classes();

        $html_icon = '';

        if (!empty($this->icon))
        {
            $html_icon = '<i class="fa fa-' . $this->icon . ' fa-fw"></i>';
        }

        $attribs = [
            'class' => implode(' ', $class),
            'href'  => $this->gethRef(),
            'role'  => 'button'
        ];

        $attributes = array_merge($attribs, $this->attributes);

        $html = "<a " . $this->attributes($attributes) . '>' . $html_icon . $this->label . '</a>';

        return $html;
    }

    /**
     *  return the classes for this button dropdown.
     */
    public function classes()
    {

        $class['btn'] = 'btn';

        $color         = $this->getColorClass();
        $class[$color] = $color;

        $size         = $this->getSizeClass();
        $class[$size] = $size;

        $parent_classes = parent::classes();

        $class += $parent_classes;

        return $class;
    }

}