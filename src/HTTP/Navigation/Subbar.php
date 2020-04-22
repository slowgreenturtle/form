<?php

namespace SGT\HTTP\Navigation;

use SGT\HTTP\Navigation\Subbar\Item;
use SGT\Traits\Config;

class Subbar
{

    use Config;

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

        $view_file = $this->configFrontEnd('navigation.subbar');

        $view = view($view_file);

        $view->items = $this->items;

        return $view->__toString();

    }
}