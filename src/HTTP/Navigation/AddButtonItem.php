<?php

namespace SGT\HTTP\Navigation;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

trait AddButtonItem
{

    public function add($item_name, $params = [])
    {

        $file_path = resource_path('navigation/buttons.json');

        $contents = File::get($file_path);

        if ($contents == null)
        {
            return null;
        }

        $list = json_decode($contents, true);

        $item = Arr::get($list, 'items.' . $item_name);

        if ($item == null)
        {
            return null;
        }

        $type  = strtolower(Arr::get($item, 'type', 'button'));
        $label = Arr::get($item, 'label');

        switch ($type)
        {

            case 'button':
                $new_item = $this->addButton($label);
                break;
        }

        $attributes = [
            'route',
            'tooltip',
            'permission',
        ];

        foreach ($attributes as $attribute)
        {

            $value = Arr::get($item, $attribute);

            switch ($attribute)
            {
                case 'route':

                    $route_param = Arr::get($params, 'route');

                    $new_item->route($value, $route_param);
                    break;
                case 'tooltip':
                    $new_item->toolTip($value);
                    break;
                case 'permission':

                    foreach ($value as $permission_item)
                    {
                        $new_item->permission($permission_item);
                    }
                    break;
            }
        }

        return $new_item;

    }

}
