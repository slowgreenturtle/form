<?php

namespace SGT\HTTP\Navigation;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use SGT\Traits\Config;

abstract class Item
{

    use Attribute;
    use Config;

    public $type = '';

    protected $icon        = '';
    protected $label       = '';
    protected $link        = '';
    protected $color       = 'blue';
    protected $size        = 'small';
    protected $permissions = [];
    protected $route       = [];
    protected $confirm     = false;
    protected $classes     = [];
    protected $colors      = [];
    protected $sizes       = [];
    protected $show_param  = true;

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

    public function canDisplay(): bool
    {

        $route = Arr::get($this->route, 'route');

        if ($route)
        {
            if (Route::current()->getName() == $route)
            {
                return false;
            }
        }

        return $this->hasPermission() && $this->canShow();
    }

    public function hasPermission()
    {

        if (count($this->permissions) < 1)
        {
            return true;
        }

        $user = auth()->user();

        if ($user)
        {
            foreach ($this->permissions as $permission)
            {
                if ($user->hasPermission($permission['slug'], $permission['context_slug'], $permission['context_id']) == true)
                {
                    return true;
                }
            }
        }

        return false;
    }

    protected function canShow()
    {

        if (is_callable($this->show_param))
        {
            $show_param = $this->show_param;

            return $show_param();
        }

        return $this->show_param == true;

    }

    /**
     *
     */
    public function show($param)
    {

        $this->show_param = $param;

        return $this;

    }

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
     *
     * @return $this
     */

    public function permission($permission_slug, $context_slug = null, $context_id = null)
    {

        if (is_array($context_slug))
        {
            $context_id   = Arr::get($context_slug, 'context_id');
            $context_slug = Arr::get($context_slug, 'context_slug');
        }

        $permission['slug']         = $permission_slug;
        $permission['context_slug'] = $context_slug;
        $permission['context_id']   = $context_id;

        $this->permissions[] = $permission;

        return $this;

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
     *
     * @return $this
     * @deprecated
     */

    public function tool_tip($tool_tip)
    {

        return $this->toolTip($tool_tip);
    }

    public function toolTip($tool_tip, $placement = null)
    {

        $placement = $this->getTooltipPlacement($placement);

        $this->attribute('data-toggle', 'tooltip');
        $this->attribute('data-placement', $placement);
        $this->attribute('data-container', 'body');

        return $this->attribute('title', $tool_tip);
    }

    public abstract function getTooltipPlacement(string $placement = null): string;

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
