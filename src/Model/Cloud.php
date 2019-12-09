<?php

namespace SGT\Model;

use Illuminate\Support\Facades\Storage;

class Cloud
{

    public static function put($file_name, $source, $visibility = 'private')
    {
        Storage::disk('s3')->put($file_name, $source, $visibility);
    }

    public static function path($file_name)
    {

        $path = config('filesystems.disks.s3.path');

        $path .= DIRECTORY_SEPARATOR . $file_name;

        return $path;

    }

    public static function files($path)
    {


        return Storage::disk('s3')->files($path);

    }

    public static function putAs($path, $source, $base_name)
    {

        Storage::disk('s3')->putFileAs(Cloud::path($path), $source, $base_name);
    }

    public static function url($file_name)
    {

        $url = config('filesystems.disks.s3.url');

        $url .= config('filesystems.disks.s3.path');

        $url .= DIRECTORY_SEPARATOR;

        $url .= $file_name;

        return $url;

    }

    public static function exists($file_name)
    {

        return Storage::disk('s3')->exists(Cloud::path($file_name));
    }

    public static function delete($file_data)
    {

        Storage::disk('s3')->delete(Cloud::path($file_data));
    }

    public static function get($file_name)
    {

        return Storage::disk('s3')->get(Cloud::path($file_name));

    }

    public static function append($file_name, $text)
    {

        Storage::disk('s3')->append(Cloud::path($file_name), $text);

    }
}


