<?php

namespace tadmin\controller;

use tadmin\support\controller\Controller;

class Captcha extends Controller
{
    public function index()
    {
        return \think\captcha\facade\Captcha::create();
    }
}
