<?php

namespace SGT\Navigation\Subbar;

use Illuminate\Support\Facades\Auth;
use SGT\Navigation\Attribute;

class Item
{

    use Attribute;

    protected $icon       = '';
    protected $label      = '';
    protected $link       = '';
    protected $color      = 'blue';
    protected $tool       = '';
    protected $size       = 'small';
    protected $attributes = [];
    protected $type       = 'item';
    protected $permission = '';

    protected $badge = [
        'text'    => '',
        'tooltip' => ''
    ];

    public function __construct($label = '')
    {

        $this->label($label);

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

    public function link($link)
    {

        $this->link = $link;

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

    public function icon($text)
    {

        $this->icon = $text;

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

        $user = Auth::user();

        if (!$user->hasAccess($this->permission))
        {
            return '';
        }

        switch ($this->type)
        {

            case 'title':
                return $this->displayTitle();
            case 'divider':
                return $this->displayDivider();
            case 'item':
                return $this->displayItem();
        }

        return '';
    }

    protected function displayTitle()
    {

        $html = '<li class="nav-title" >' . $this->label . '</li >';

        return $html;
    }

    protected function displayDivider()
    {

        $html = '<li class="divider" ></li >';

        return $html;
    }

    protected function displayItem()
    {

        $html = '<li class="nav-item" ><a class="nav-link" href = "' . $this->link . '" >';

        if ($this->icon != '')
        {
            $html .= '<i class="icon-calculator"></i>';
        }

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

}