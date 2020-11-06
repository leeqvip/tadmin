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
    /*
     *Default Tauthz enforcer
     */
    'enforcer' => [
        'model_config_path' => __DIR__ . '/casbin-model.conf',
        'adapter' => tadmin\service\casbin\Adapter::class,
    ],

    // 模板配置
    'view' => [
        // 模板引擎类型使用Think
        'type'          => 'Think',
        // 默认模板渲染规则 1 解析为小写+下划线 2 全部转换小写 3 保持操作方法
        'auto_rule'     => 1,
        // 模板目录名
        'view_dir_name' => 'view',
        // 模板文件路径
        'view_path' => admin_view_path(),
        // 模板后缀
        'view_suffix'   => 'html',
        // 模板文件名分隔符
        'view_depr'     => DIRECTORY_SEPARATOR,
        // 模板引擎普通标签开始标记
        'tpl_begin'     => '{',
        // 模板引擎普通标签结束标记
        'tpl_end'       => '}',
        // 标签库标签开始标记
        'taglib_begin'  => '{',
        // 标签库标签结束标记
        'taglib_end'    => '}',

        // 公共目录
        'public_name' => 'public',

        'tpl_replace_string' => [
            '__TADMIN_ASSETS__' => '/tmp/assets',
        ],
    ],
];
