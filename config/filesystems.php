<?php

return [
    // 默认的文件存储磁盘
    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
     */

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => public_path('uploads/'),
            'url' => '/uploads/',
        ],
    ],
];
