<?php

namespace SGT\Traits;

trait Config
{

    public function configFrontEnd($path)
    {

        $bootstrap_version = config('sgtform.config.bootstrap.version');

        $field = 'sgtform.bootstrap.' . $bootstrap_version . '.' . $path;

        return config($field);

    }

    public function config($path)
    {

        $field = 'sgtform.' . $path;

        return config($field);

    }

}