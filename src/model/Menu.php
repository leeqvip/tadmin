<?php

namespace tadmin\model;

class Menu extends Model
{
    use traits\Tree;

    protected $name = 'menus';
}
