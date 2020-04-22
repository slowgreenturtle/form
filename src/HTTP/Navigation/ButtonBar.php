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

        $content = [];

        foreach ($this->items as $item)
        {
            if ($item->canDisplay())
            {
                $content[] = $item->display();
            }
        }

        return implode('&nbsp;', $content);

    }

    public static function create()
    {

        return new ButtonBar();
    }

}