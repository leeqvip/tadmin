<?php

namespace tadmin\model;

use think\model\Pivot;
use Db;

class AdminerRole extends Pivot
{
    use traits\ModelHelper;

    protected $table = 'adminers_roles';

    public function __construct($data = [], $parent = null, $table = '')
    {
        $this->parent = $parent;

        if (is_null($this->name)) {
            $this->name = $table;
        }

        parent::__construct($data, $parent, $table);

        if ($this->table) {
            $this->table = Db::getConfig('prefix').$this->table;
        }
    }
}
