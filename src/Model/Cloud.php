<?php

namespace SGT\Model;

use Illuminate\Support\Facades\Storage;

class Cloud
{

    /**
     * @param        $file_name
     * @param        $source
     * @param string $visibility
     */
    public static function put($file_name, $source, $visibility = 'private', $disk = 's3')
    {

        Storage::disk($disk)->put($file_name, $source, $visibility);
    }

    public static function files($path, $disk = 's3')
    {


        return Storage::disk($disk)->files($path);

    }

    public static function putAs($path, $source, $base_name, $disk = 's3')
    {

        Storage::disk($disk)->putFileAs(Cloud::path($path), $source, $base_name);
    }

    public static function path($file_name, $disk = 's3')
    {

        $config_path = "filesystems.disks.{$disk}.path";

        $path = config($config_path);

        $path .= DIRECTORY_SEPARATOR . $file_name;

        return $path;

    }

    public static function url($file_name, $disk = 's3')
    {

        $config_url = "filesystems.disks.{$disk}.url";
        $url        = config($config_url);

        $config_path = "filesystems.disks.{$disk}.path";
        $url         .= config($config_path);

        $url .= DIRECTORY_SEPARATOR;

        $url .= $file_name;

        return $url;

    }

    public static function exists($file_name, $disk = 's3')
    {

        return Storage::disk($disk)->exists(Cloud::path($file_name));
    }

    public static function delete($file_data, $disk = 's3')
    {

        Storage::disk($disk)->delete(Cloud::path($file_data));
    }

    public static function get($file_name, $disk = 's3')
    {

        return Storage::disk($disk)->get(Cloud::path($file_name));

    }

    public static function append($file_name, $text, $disk = 's3')
    {

        Storage::disk($disk)->append(Cloud::path($file_name), $text);

    }
}