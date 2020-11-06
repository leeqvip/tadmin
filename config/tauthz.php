<?php

return [
    'enforcers' => [
        'tadmin' => [
            /*
            * Model 设置
            */
            'model' => [
                // 可选值: "file", "text"
                'config_type' => 'file',
                'config_file_path' => __DIR__ . '/casbin-model.conf',
            ],

            // 适配器 .
            'adapter' => \tadmin\service\casbin\Adapter::class,
        ],
    ],
];