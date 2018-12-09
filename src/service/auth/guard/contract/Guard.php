<?php

namespace tadmin\service\auth\guard\contract;

use tadmin\service\auth\contract\Authenticate;

interface Guard
{
    public function login(Authenticate $authenticate);

    public function logout();

    public function getName();
}
