<?php

/**
 * A horizontal bar of buttons. Can include dropdown buttons.
 */

namespace SGT\HTTP\Navigation;

class ButtonBar
{

    protected $items = [];

    public function addButton($label)
    {

        $item = new Button($label);

        $this->items[] = $item;

        return $item;

    }

    public function display()
    {

        $html = '';

        foreach ($this->items as $item)
        {

            if ($item->hasPermission())
            {
                $html .= $item->display() . '&nbsp;';
            }

        }

        return $html;

    }

    public static function create()
    {

        return new ButtonBar();
    }

}