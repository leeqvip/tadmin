<?php
// +----------------------------------------------------------------------
// | 控制台配置
// +----------------------------------------------------------------------
return [
    // 指令定义
    'commands' => [
        'tadmin:init' => \tadmin\command\Init::class,
        'tadmin:migrate:run' => \tadmin\command\Migrate::class,
    ],
];
