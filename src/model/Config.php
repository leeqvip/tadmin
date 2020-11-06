<?php

namespace tadmin\model;

class Config extends Model
{
    protected $name = 'configs';

    public static $configs = [];

    public static function get($key)
    {
        if (isset(self::$configs[$key])) {
            return self::$configs[$key];
        }
        self::$configs = self::column('value', 'key');

        return isset(self::$configs[$key]) ? self::$configs[$key] : '';
    }
}
