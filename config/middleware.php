<?php

return [
    'tadmin.admin' => [
        \tadmin\middleware\AuthCheck::class,
        \tadmin\middleware\PermissionCheck::class,
        \tadmin\middleware\LogRecord::class,
    ],
];
