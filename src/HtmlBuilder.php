<?php

namespace SGT;

class HtmlBuilder
{

    public function menu($menu)
    {

        $html = '';

        foreach ($menu as $menu_item)
        {

            $html .= $this->menu_item($menu_item);

        }

        return $html;

    }

    public function menu_item($dropdown)
    {

        $sizes = [
            'xsmall'  => 'btn-xs',
            'small'   => 'btn-sm',
            'large'   => 'btn-lg',
            'default' => ''
        ];

        $title = array_get($dropdown, 'title');
        $links = array_get($dropdown, 'links');
        $size  = array_get($dropdown, 'size', 'default');

        $size = array_get($sizes, $size, '');

        $button_classes = [
            'btn',
            'btn-primary',
            'dropdown-toggle',
        ];

        if (!empty($size))
        {
            $button_classes[] = $size;
        }

        $class_html = implode(' ', $button_classes);

        $button_attributes =
            [
                'type'          => 'button',
                'class'         => $class_html,
                'data-toggle'   => 'dropdown',
                'aria-haspopup' => 'true',
                'aria-expanded' => 'false'
            ];

        $button_attribute_html = $this->arrayAttributes($button_attributes);

        $html = '<div class="btn-group">';
        $html .= "<button $button_attribute_html>$title<span class=\"caret\"></span></button>";
        $html .= '<ul class="dropdown-menu dropdown-menu-right">';

        foreach ($links as $action)
        {

            if (is_array($action))
            {

                $type = array_get($action, 'type');

                switch ($type)
                {
                    case 'confirm':
                        $html .= $this->confirm($action);
                        break;
                    default:
                        $link       = array_get($action, 'link');
                        $text       = array_get($action, 'text');
                        $attributes = array_get($action, 'attributes', []);
                        $html       .= '<li>' . $this->link($link, $text, $attributes) . '</li>';
                        break;
                }
            }
            else
            {
                switch ($action)
                {
                    case 'divider':
                        $html .= '<li class="divider"></li>';
                        break;
                }
            }
        }

        $html .= '</ul></div>';

        return $html;

    }

    /**
     * Build an HTML attribute string from an array.
     *
     * @param  array $attributes
     * @return string
     */
    public function arrayAttributes($attributes)
    {

        $html = [];

        // For numeric keys we will assume that the key and the value are the same
        // as this will convert HTML attributes such as "required" to a correct
        // form like required="required" instead of using incorrect numerics.
        foreach ((array)$attributes as $key => $value)
        {
            $element = $this->arrayAttributeElement($key, $value);

            if (!is_null($element))
            {
                $html[] = $element;
            }
        }

        return count($html) > 0 ? ' ' . implode(' ', $html) : '';
    }

    /**
     * Build a single attribute element.
     *
     * @param  string $key
     * @param  string $value
     * @return string
     */
    protected function arrayAttributeElement($key, $value)
    {

        if (is_numeric($key))
        {
            $key = $value;
        }

        $return = null;

        if (!is_null($value))
        {
            if (is_array($value))
            {
                $return = $key . '=' . json_encode($value);
            }
            else
            {
                $return = $key . '="' . e($value) . '"';
            }

        }

        return $return;
    }

    public function confirm($action)
    {

        $title = array_get($action, 'name');
        $link  = array_get($action, 'link');

        $unique_id = str_random();

        $attributes['class'] = $unique_id;

        $html = '<li>' . $this->link('#', $title, $attributes) . '</li>';

        $html .= "<script>

        jQuery(document).on(\"click\", \".{$unique_id}\", function(e) {

            bootbox alert(\"$title?\", function()
            {
                location.href = \"$link\";
            });
        });
        </script >";

        return $html;
    }

    public function link($url, $title = null, $attributes = [], $secure = null, $escape = true)
    {

        $url = $this->url->to($url, [], $secure);

        if (is_null($title) || $title === false)
        {
            $title = $url;
        }

        $icon = array_get($attributes, 'icon');

        $icon_text = '';

        if ($icon != null)
        {
            unset($attributes['icon']);
            $icon_text = '<i class="fa fa-' . $icon . ' fa-fw"></i>';

        }

        return '<a href="' . $url . '"' . $this->attributes($attributes) . '>' . $icon_text . $this->entities($title) . '</a>';

    }

    public function dropdown_button($title, $actions)
    {

        $html = '<div class="btn-group">';
        $html .= '<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
        $html .= $title . '<span class="caret"></span>';
        $html .= '</button>';

        $html .= '<ul class="dropdown-menu">';

        foreach ($actions as $action)
        {

            if (is_array($action))
            {

                $type = array_get($action, 'type');

                switch ($type)
                {
                    case 'confirm':
                        $html .= $this->confirm($action);
                        break;
                    default:
                        $link       = array_get($action, 'link');
                        $title      = array_get($action, 'name');
                        $attributes = array_get($action, 'attributes', []);

                        $html .= '<li>' . $this->link($link, $title, $attributes) . '</li>';
                        break;
                }
            }
            else
            {
                switch ($action)
                {
                    case 'divider':
                        $html .= '<li class="divider"></li>';
                        break;
                }
            }
        }

        $html .= '</ul></div>';

        return $html;

    }

    public function buttons($buttons)
    {

        $html = '';

        foreach ($buttons as $button)
        {

            $html .= $button->display();
            $html .= '&nbsp;';

        }

        return $html;

    }

}