<?php

namespace tadmin\controller;

use tadmin\support\controller\Controller;

class Captcha extends Controller
{
    public function index()
    {
        $oldLevel = error_reporting();
        error_reporting($oldLevel & ~E_DEPRECATED);
        try {
            return \think\captcha\facade\Captcha::create();
        } finally {
            error_reporting($oldLevel);
        }
    }
}
