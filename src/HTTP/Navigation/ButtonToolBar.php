<?php

/**
 * A horizontal bar of buttons. Can include dropdown buttons.
 */

namespace SGT\HTTP\Navigation;

use Illuminate\Support\Arr;
use SGT\Traits\Config;

class ButtonToolBar
{

    use Config;
    use AddItem;

    protected $items                     = [];
    protected $size                      = 'small';
    protected $classes                   = [];
    protected $config_button_group_sizes = 'element.button.toolbar.sizes';

    public function __construct()
    {

        $this->sizes = $this->configFrontEnd($this->config_button_group_sizes);

        $this->addClass('btn-toolbar');
    }

    public function addClass($class)
    {

        $this->classes[$class] = $class;

        return $this;
    }

    public function addSubmit($label)
    {

        $item = new Submit($label);

        $this->items[] = $item;

        return $item;
    }

    public function addButton($label)
    {

        $item = new Button($label);

        $this->items[] = $item;

        return $item;

    }

    public function addGroup()
    {

        $item = new ButtonBar();

        $this->items[] = $item;

        return $item;

    }

    public function addDropdown()
    {

        $item = new ButtonDropdown();

        $this->items[] = $item;

        return $item;

    }

    public function getSizeClass()
    {

        return Arr::get($this->sizes, $this->size);

    }

    public function size($size)
    {

        $this->size = $size;

        return $this;
    }

    public function display()
    {

        $content = [];

        foreach ($this->items as $item)
        {
            $content[] = $item->display();
        }

        $classes = $this->classes();
        $classes = implode(' ', $classes);

        return '<div class="' . $classes . '" role="toolbar" aria-label="Navigation">' . implode('', $content) . '</div>';

    }

    public function classes()
    {

        return $this->classes;
    }

    public static function create()
    {

        return new ButtonBar();
    }

}
