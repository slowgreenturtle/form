<?php

namespace SGT\Navigation;

use SGT\Navigation\Sidebar\Item;

class Subbar
{

    public $title = '';
    public $menus = [];

    public function title($label)
    {

        $item = Item::create($label);
        $item->type('title');
        $this->menus[] = $item;

        return $item;

    }

    public function item($label)
    {

        $item          = Item::create($label);
        $this->menus[] = $item;

        return $item;

    }

    public function divider()
    {

        $item = Item::create();
        $item->type('divider');
        $this->menus[] = $item;

        return $item;

    }
}