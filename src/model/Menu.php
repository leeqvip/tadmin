<?php

namespace tadmin\model;

class Menu extends Model
{
    use traits\Tree;

    protected $table = 'menus';
}
