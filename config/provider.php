<?php

return [
    \tadmin\service\upload\contract\Factory::class => \tadmin\service\upload\Uploader::class,
    \tadmin\service\auth\contract\Authenticate::class => \tadmin\model\Adminer::class,
    \tadmin\service\auth\guard\contract\Guard::class => \tadmin\service\auth\guard\SessionGuard::class,
    \tadmin\service\auth\contract\Auth::class => \tadmin\service\auth\Auth::class,
];
