<?php

namespace tadmin\service\auth\contract;

use think\Request;

interface Auth
{
    public function login(Request $request);
}
