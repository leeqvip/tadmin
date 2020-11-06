<?php

namespace tadmin\support\view;

use think\helper\Arr;

class View extends \think\View
{
    protected function resolveConfig(string $name)
    {
        $config = $this->app->config->get('tadmin.view', []);
        Arr::forget($config, 'type');
        return $config;
    }
}
