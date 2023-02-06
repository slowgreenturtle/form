<?php

namespace SGT\HTTP\Navigation;

use SGT\Traits\Config;

class ButtonDropdown
{

    use Config;
    use AddItem;

    public    $type      = 'button_dropdown';
    protected $alignment = 'right';
        protected $color     = null;     //  Can be left or right
protected $items     = [];
    protected $size      = null;

    public static function create()
    {

        return new ButtonDropdown();
    }

    public function addButton($label)
    {

        return $this->addItem($label);
    }

    public function addDivider()
    {

        $item          = new Divider();
        $this->items[] = $item;

        return $item;
    }

    public function addItem($label)
    {

        $item          = new Menu($label);
        $this->items[] = $item;

        return $item;

    }

    public function alignment($alignment = 'left')
    {

        $this->alignment = $alignment == 'left' ? 'left' : 'right';

        return $this;

    }

    public function color($color)
    {

        $this->color = $color;

        return $this;
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
                $button = $this->makeButton($item);

                if ($this->color != null)
                {
                    $button->color($this->color);
                }

                if ($this->size != null)
                {
                    $button->size($this->size);
                }

            }
            else
            {
                $items[] = $item;
                $item->addClass('dropdown-item');
            }

            $item_count++;

        }

        if ($item_count > 0)
        {

            $view->dropdown = $this;
            $view->items    = $items;
            $view->button   = $button;

            return $view->__toString();
        }

        return '';

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

    public function size($size)
    {

        $this->size = $size;

        return $this;
    }

    protected function makeButton($item)
    {

        $button = new Button();

        $button->label($item->getLabel());
        $button->link($item->gethRef());

        $attributes = $item->attributes();

        foreach ($attributes as $key => $value)
        {
            $button->attribute($key, $value);
        }

        return $button;

    }

}