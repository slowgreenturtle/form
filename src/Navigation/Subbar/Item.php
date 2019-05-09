<?php

namespace SGT\Navigation\Subbar;

use Illuminate\Support\Facades\Auth;
use SGT\Navigation\Attribute;

class Item
{

    use Attribute;

    protected $label      = '';
    protected $url        = '';
    protected $color      = 'blue';
    protected $tool       = '';
    protected $size       = 'small';
    protected $attributes = [];
    protected $type       = 'item';
    protected $permission = '';
    protected $colors     = [];
    protected $sizes      = [];

    protected $badge = [
        'text'    => '',
        'tooltip' => ''
    ];

    public function __construct($label = '')
    {

        $this->label($label);
        $this->label($label);
        $this->colors = config('sgtform.colors');
        $this->sizes  = config('sgtform.sizes');

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

        $this->permission = $permission;

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

        $user = auth()->user();

        $permission = $this->permission;

        if (!empty($permission) && !$user->hasPermission($permission))
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
            return route(array_get($this->route, 'route'), array_get($this->route, 'params'));
        }

        return $this->url;
    }
}