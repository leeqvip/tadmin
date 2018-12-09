<?php

namespace tadmin\model;

use think\Model as Base;
use Db;

abstract class Model extends Base
{
    public function __construct($data = [])
    {
        parent::__construct($data);
        //TODO:初始化内容
        if ($this->table) {
            $this->table = Db::getConfig('prefix').$this->table;
        }
    }

    public function updateOrCreate(array $attributes, array $values = [])
    {
        $first = $this->where($attributes)->find();
        if ($first) {
            $first->data($values);
            $first->save();

            return $first;
        } else {
            return self::create($values, true);
        }
    }
}
