<?php

namespace tadmin\controller;

use tadmin\support\controller\Controller;

class Transfer extends Controller
{
    public function message($value = null)
    {
        $this->error($value);
    }
}
