<?php

namespace SGT\HTTP\Navigation;

use SGT\HTTP\Config;
use SGT\HTTP\Navigation\Subbar\Item;

class Subbar
{

    use Config;
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

        $view_file = $this->config('navigation.subbar');

        $view = view($view_file);

        $view->items = $this->items;

        return $view->__toString();

    }
}