<?php

namespace SGT\Navigation;

class ButtonDropdown extends Button
{

    public $type = 'button_dropdown';

    protected $items = [];

    public function addItem($label)
    {

        $item = new Menu($label);

        $this->items[] = $item;

        return $item;

    }

    public function addDivider()
    {

        $item          = new Divider();
        $this->items[] = $item;

        return $item;
    }

    public function display()
    {

        $view = view(config('sgtform.navigation.button.dropdown'));

        $view->dropdown = $this;

        $divider    = null;
        $item_count = 0;

        $items = [];

        foreach ($this->items as $item)
        {

            if (!$item->hasPermission())
            {
                continue;
            }

            if ($item->type == 'divider')
            {
                if ($divider)
                {
                    continue;
                }

                # we flag having a divider, but don't add it yet because we don't know if there's a next item
                $divider = $item;
                continue;

            }

            if ($divider && count($items) > 0)
            {
                $items[] = $divider;
                $divider = null;
            }

            if ($item_count == 0)
            {
                $this->label($item->getLabel());
                $this->link($item->gethRef());
            }
            else
            {
                $items[] = $item;
            }

            $item_count++;

        }

        if ($item_count > 0)
        {
            $view->items = $items;

            return $view->__toString();
        }

        return '';

    }

    public static function create($label = '')
    {

        return new ButtonDropdown();
    }

}