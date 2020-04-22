<?php

namespace SGT\HTTP\Navigation;

class Submit extends Button
{

    public static function create($label = '')
    {

        return new Submit($label);
    }

    public function display()
    {

        if ($this->canDisplay() == false)
        {
            return '';
        }

        $html_icon = '';

        if (!empty($this->icon))
        {
            $html_icon = '<i class="fa fa-' . $this->icon . ' fa-fw"></i>';
        }

        $classes = $this->classes();

        $attribs = [
            'class' => implode(' ', $classes),
        ];

        $this->attribute('type', 'submit');

        $attributes = array_merge($attribs, $this->attributes);

        $html = "<button " . $this->attributes($attributes) . '>' . $html_icon . $this->label . '</button>';

        return $html;
    }
}