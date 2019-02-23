<?php

use tadmin\model\Config;

if (!function_exists('script_path')) {
    function script_path()
    {
        if ('cli' == PHP_SAPI) {
            $scriptName = realpath($_SERVER['argv'][0]);
        } else {
            $scriptName = $_SERVER['SCRIPT_FILENAME'];
        }

        return realpath(dirname($scriptName)).'/';
    }
}

if (!function_exists('app_path')) {
    function app_path($path = '')
    {
        return env('app_path').ltrim($path, '/');
    }
}

if (!function_exists('public_path')) {
    function public_path($path = '')
    {
        return script_path().ltrim($path, '/');
        // return app_path('../public/').ltrim($path, '/');
    }
}

if (!function_exists('admin_path')) {
    function admin_path($path = '')
    {
        return __DIR__.'/'.ltrim($path, '/');
    }
}

if (!function_exists('admin_config_path')) {
    function admin_config_path($path = '')
    {
        return admin_path('config/').ltrim($path, '/');
    }
}

if (!function_exists('admin_route_path')) {
    function admin_route_path($path = '')
    {
        return admin_path('route/').ltrim($path, '/');
    }
}

if (!function_exists('admin_view_path')) {
    function admin_view_path($path = '')
    {
        return admin_path('resource/view/').ltrim($path, '/');
    }
}

if (!function_exists('site_config')) {
    function site_config($key)
    {
        return Config::get($key);
    }
}

if (!function_exists('array_deep_merge')) {
    function array_deep_merge(array $a, array $b)
    {
        foreach ($a as $key => $val) {
            if (isset($b[$key])) {
                if (gettype($a[$key]) != gettype($b[$key])) {
                    continue;
                }
                if (is_array($a[$key])) {
                    $a[$key] = array_deep_merge($a[$key], $b[$key]);
                } else {
                    $a[$key] = $b[$key];
                }
            }
        }

        return $a;
    }
}

\think\Console::addDefaultCommands([
    'tadmin:init' => \tadmin\command\Init::class,
    'tadmin:migrate:run' => \tadmin\command\Migrate::class,
]);
