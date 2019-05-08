<?php

namespace SGT\Navigation;

use SGT\Navigation\Subbar\Item;

class Subbar
{

    public $title = '';
    public $items = [];

    public function item($label)
    {

        $item          = Item::create($label);
        $this->items[] = $item;

        return $item;

    }

    public function divider()
    {

        $item = Item::create();
        $item->type('divider');
        $this->items[] = $item;

        return $item;

    }

    public function display()
    {

        $html = '';

        foreach ($this->items as $item)
        {
            $html .= $item->display();
        }

        return $html;

    }
}