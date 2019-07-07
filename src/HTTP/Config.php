<?php

namespace SGT\HTTP;

trait Config
{

    public function config($path)
    {

        $bootstrap_version = config('sgtform.config.bootstrap.version');

        $field = 'sgtform.bootstrap.' . $bootstrap_version . '.' . $path;

        return config($field);

    }

}