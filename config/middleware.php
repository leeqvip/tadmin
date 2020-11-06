<?php

return [
    "alias" => [
        'tadmin.admin' => [
            \tadmin\middleware\AuthCheck::class,
            \tadmin\middleware\PermissionCheck::class,
            \tadmin\middleware\LogRecord::class,
        ],
    ]
];
