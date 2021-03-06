<?php

namespace SGT\HTTP\Navigation\Subbar;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use SGT\HTTP\Navigation\Attribute;

class Item
{

    use Attribute;

    protected $label       = '';
    protected $url         = '';
    protected $color       = 'blue';
    protected $tool        = '';
    protected $size        = 'small';
    protected $attributes  = [];
    protected $type        = 'item';
    protected $permissions = [];
    protected $colors      = [];
    protected $sizes       = [];

    protected $badge = [
        'text'    => '',
        'tooltip' => ''
    ];

    public function __construct($label = '')
    {

        $this->label($label);
        $this->label($label);
        //$this->colors = configFrontEnd('sgtform.colors');
        //$this->sizes  = configFrontEnd('sgtform.sizes');

    }

    public function label($text)
    {

        $this->label = $text;

        return $this;
    }

    public function route($route, $params = [])
    {

        $this->route['route']  = $route;
        $this->route['params'] = $params;

        return $this;
    }

    public function permission($permission)
    {

        $this->permissions[] = $permission;

        return this;
    }

    public function url($url)
    {

        $this->link = $url;

        return $this;
    }

    public function badge($text, $tooltip = '')
    {

        $this->badge['text']    = $text;
        $this->badge['tooltip'] = $tooltip;

        return $this;
    }

    public static function create($label = '')
    {

        return new Item($label);
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

    public function tool($text)
    {

        $this->tool = $text;

        return $this;
    }

    public function attribute($title, $value)
    {

        $this->attributes[$title] = $value;

        return $this;
    }

    public function display()
    {

        if ($this->hasPermission() == false)
        {
            return '';
        }

        switch ($this->type)
        {
            case 'divider':
                return $this->displayDivider();
            case 'item':
                return $this->displayItem();
        }

        return '';
    }

    public function hasPermission()
    {

        $user = auth()->user();

        $permissions = $this->permissions;

        if (count($permissions) < 1)
        {
            return true;
        }

        foreach ($permissions as $permission)
        {
            if ($user->hasPermission($permission))
            {
                return true;
            }
        }

        return false;

    }

    protected function displayDivider()
    {

        $html = '<li class="divider" ></li >';

        return $html;
    }

    protected function displayItem()
    {

        $html = '<a class="nav-link" href="' . $this->gethRef() . '" >';

        $html .= $this->label;

        $badge_text = $this->badge['text'];

        if ($badge_text != '')
        {
            $title = $this->badge['tooltip'];

            $title = $title == '' ? '' : "title=\"$title\"";

            $html .= '<span class="badge badge-info" ' . $title . ' >' . strtoupper($badge_text) . '</span>';
        }

        $html .= '</a></li>';

        return $html;
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

        return $this->url;
    }
}