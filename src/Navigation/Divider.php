<?php

namespace SGT\Navigation;

class Divider extends Item
{

    public $type = 'divider';

    public static function create($link = '')
    {

        return new Divider($link);
    }

    public function display()
    {

        return '<hr>';
    }
}