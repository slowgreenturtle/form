<?php
/**
 * A single Menu Item. Which displays a href tag.
 */

namespace SGT\HTTP\Navigation;

class Menu extends Item
{

    public    $type  = 'menu';
    protected $color = '';
    protected $size  = '';

    protected $config_colors = 'element.link.colors';
    protected $config_sizes  = 'element.link.sizes';

    public static function create($link = '')
    {

        return new Menu($link);
    }

    public function display()
    {

        $html_icon = '';

        if (!empty($this->icon))
        {
            $html_icon = '<i class="fa fa-' . $this->icon . ' fa-fw"></i>';
        }

        $class = $this->classes();

        $attribs = [
            'class' => implode(' ', $class),
            'href'  => $this->gethRef()
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

        $class = [];

        $color = $this->getColorClass();

        if ($color)
        {
            $class[$color] = $color;
        }

        $size = $this->getSizeClass();

        if ($size)
        {
            $class[$size] = $size;
        }

        $parent_classes = parent::classes();

        $class += $parent_classes;

        return $class;
    }

}