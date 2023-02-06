<?php

namespace SGT\HTTP\Navigation;

class Divider extends Item
{

    public $type = 'divider';

    protected $config_colors = 'sgtform.element.link.colors';
    protected $config_sizes  = 'sgtform.element.link.sizes';

    public static function create($link = '')
    {

        return new Divider($link);
    }

    public function display()
    {

        return '<hr>';
    }

    public function getTooltipPlacement(string $placement = null): string
    {

        if ($placement == null)
        {
            $placement = $this->configFrontEnd('element.link.tooltip.placement');
        }

        return $placement;
    }

}