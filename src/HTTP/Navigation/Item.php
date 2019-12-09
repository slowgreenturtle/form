<?php

namespace SGT\HTTP\Navigation;

use Illuminate\Support\Arr;
use SGT\Traits\Config;

abstract class Item
{

    use Attribute;
    use Config;

    public $type = '';

    protected $icon       = '';
    protected $label      = '';
    protected $link       = '';
    protected $color      = 'blue';
    protected $size       = 'small';
    protected $attributes = [];
    protected $permission = [];
    protected $route      = [];
    protected $confirm    = false;
    protected $classes    = [];
    protected $colors     = [];
    protected $sizes      = [];

    public function __construct($label = 'Default')
    {

        $this->label($label);
        $this->colors = $this->configFrontEnd($this->config_colors);
        $this->sizes  = $this->configFrontEnd($this->config_sizes);

    }

    public function label($label)
    {

        $this->label = $label;

        return $this;
    }

    public abstract function display();

    abstract public static function create($label = '');

    public function getSizeClass()
    {

        return Arr::get($this->sizes, $this->size);

    }

    public function getColorClass()
    {

        return Arr::get($this->colors, $this->color, '');
    }

    public function name($value)
    {

        return $this->attribute('name', $value);
    }

    public function attribute($title, $value)
    {

        $this->attributes[$title] = $value;

        return $this;
    }

    public function value($value)
    {

        return $this->attribute('value', $value);
    }

    public function getLabel()
    {

        return $this->label;
    }

    public function confirm($set = true)
    {

        $this->confirm = $set;

        return $this;
    }

    /**
     * @param      $permission_slug The slug of the permission to be tested
     * @param null $context_slug    The type of variable being passed in.
     * @param null $context_id      The id of the variable type to test against if looking for a single variable.
     * @return $this
     */

    public function permission($permission_slug, $context_slug = null, $context_id = null)
    {

        if (is_array($context_slug))
        {
            $context_id   = Arr::get($context_slug, 'context_id');
            $context_slug = Arr::get($context_slug, 'context_slug');
        }

        $this->permission['slug']         = $permission_slug;
        $this->permission['context_slug'] = $context_slug;
        $this->permission['context_id']   = $context_id;

        return $this;

    }

    public function hasPermission()
    {

        if (count($this->permission) < 1)
        {
            return true;
        }

        $user = auth()->user();

        if ($user)
        {
            return $user->hasPermission($this->permission['slug'], $this->permission['context_slug'], $this->permission['context_id']);
        }

        return false;
    }

    public function route($route, $params = [])
    {

        $this->route['route']  = $route;
        $this->route['params'] = $params;

        return $this;
    }

    public function link($link)
    {

        $this->link = $link;

        return $this;
    }

    public function getLink()
    {

        return $this->link;
    }

    public function type($type)
    {

        $this->type = $type;

        return $this;
    }

    public function color($color)
    {

        $this->color = $color;

        return $this;
    }

    public function addClass($class)
    {

        $this->classes[$class] = $class;

        return $this;
    }

    public function classes()
    {

        return $this->classes;
    }

    public function icon($text)
    {

        $this->icon = $text;

        return $this;
    }

    /**
     * Attach a tool tip to the object
     *
     * @param $text
     * @return $this
     */

    public function tool_tip($tool_tip)
    {

        return $this->attribute('title', $tool_tip);
    }

    public function size($size)
    {

        $this->size = $size;

        return $this;
    }

    /**
     * Will build an href for this item with the route being a priority if it exists. if not, the link path
     * will be used.
     *
     * @return string
     */
    public function gethRef()
    {

        if (count($this->route))
        {
            return route(Arr::get($this->route, 'route'), Arr::get($this->route, 'params'));
        }

        return $this->link;
    }
}