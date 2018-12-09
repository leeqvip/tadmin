<?php

use tadmin\model\Config;

function script_path()
{
    if ('cli' == PHP_SAPI) {
        $scriptName = realpath($_SERVER['argv'][0]);
    } else {
        $scriptName = $_SERVER['SCRIPT_FILENAME'];
    }

    return realpath(dirname($scriptName)).'/';
}

function app_path($path = '')
{
    return env('app_path').ltrim($path, '/');
}

function public_path($path = '')
{
    return script_path().ltrim($path, '/');
    // return app_path('../public/').ltrim($path, '/');
}

function admin_path($path = '')
{
    return __DIR__.'/'.ltrim($path, '/');
}

function admin_config_path($path = '')
{
    return admin_path('config/').ltrim($path, '/');
}

function admin_route_path($path = '')
{
    return admin_path('route/').ltrim($path, '/');
}

function admin_view_path($path = '')
{
    return admin_path('resource/view/').ltrim($path, '/');
}

function site_config($key)
{
    return Config::get($key);
}

\think\Console::addDefaultCommands([
    'tadmin:init' => \tadmin\command\Init::class,
    'tadmin:migrate:run' => \tadmin\command\Migrate::class,
]);
