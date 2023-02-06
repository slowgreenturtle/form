<?php

namespace SGT\HTTP\Navigation;

class Button extends Item
{

    public $type = 'button';

    protected $config_colors = 'element.button.colors';
    protected $config_sizes  = 'element.button.sizes';

    public static function create($label = '')
    {

        return new Button($label);
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

    public function display()
    {

        if ($this->canDisplay() == false)
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

        $attributes = array_merge($attribs, $this->attributes());

        $html = "<a " . $this->htmlAttributes($attributes) . '>' . $html_icon . $this->label . '</a>';

        return $html;
    }

    public function getTooltipPlacement(string $placement = null): string
    {

        if ($placement == null)
        {
            $placement = $this->configFrontEnd('element.button.tooltip.placement');
        }

        return $placement;
    }

}
