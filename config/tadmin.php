<?php
/**
 * Tadmin 配置.
 *
 * 该配置为默认配置
 * 如在thinkphp框架中config目录下的tadmin.php中的配置优先级高于此配置
 */
return [
    'template' => [
        // 视图目录
        'view_path' => admin_view_path(),
        // public目录名
        'public_name' => 'public',
        'tpl_replace_string' => [
            '__TADMIN_ASSETS__' => '/tmp/assets',
        ],
    ],
];
