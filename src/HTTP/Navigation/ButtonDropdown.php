<?php

namespace SGT\HTTP\Navigation;

class ButtonDropdown extends Button
{

    public    $type      = 'button_dropdown';
    protected $alignment = 'right';
    protected $items     = [];     //  Can be left or right

    public function alignment($alignment = 'left')
    {

        $this->alignment = $alignment == 'left' ? 'left' : 'right';

        return $this;

    }

    public function addItem($label)
    {

        $item = new Menu($label);
        $item->addClass('dropdown-item');
        $this->items[] = $item;

        return $item;

    }

    public function addDivider()
    {

        $item          = new Divider();
        $this->items[] = $item;

        return $item;
    }

    public function dropdownMenuClasses()
    {

        $classes["dropdown-menu"] = "dropdown-menu";

        if ($this->alignment == 'right')
        {
            $classes["dropdown-menu-right"] = "dropdown-menu-right";
        }

        return implode(' ', $classes);

    }

    public function display()
    {

        $view = view($this->configFrontEnd('navigation.button.dropdown'));

        $divider    = null;
        $item_count = 0;

        $items = [];

        foreach ($this->items as $item)
        {

            if ($item->canDisplay() == false)
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

            $view->items    = $items;
            $view->dropdown = $this;

            return $view->__toString();
        }

        return '';

    }

    public static function create($label = '')
    {

        return new ButtonDropdown();
    }

}