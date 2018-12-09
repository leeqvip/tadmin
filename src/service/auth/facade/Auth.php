<?php

namespace tadmin\service\auth\facade;

use think\Facade;

class Auth extends Facade
{
    protected static function getFacadeClass()
    {
        return \tadmin\service\auth\Auth::class;
    }
}
