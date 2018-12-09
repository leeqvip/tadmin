<?php
/**
 * Tadmin 配置.
 *
 * 该配置为默认配置
 * 如在thinkphp框架中config目录下的tadmin.php中的配置优先级高于此配置
 */
return [
    'template' => [
        'view_path' => admin_view_path(),
        'tpl_replace_string' => [
            '__TADMIN_ASSETS__' => '/tmp/assets',
        ],
    ],
];
