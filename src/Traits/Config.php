<?php

namespace SGT\Traits;

trait Config
{

    public function configFrontEnd($path, $default = null)
    {

        $bootstrap_version = config('sgtform.config.bootstrap.version');

        $field = 'sgtform.bootstrap.' . $bootstrap_version . '.' . $path;

        return config($field, $default);

    }

    public function config($path, $default = null)
    {

        $field = 'sgtform.' . $path;

        return config($field, $default);

    }

}