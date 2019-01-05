<?php

namespace tadmin\model;

use think\Model as Base;
use Db;

abstract class Model extends Base
{
    protected $autoWriteTimestamp = 'datetime';

    protected $createTime = 'created_at';

    protected $updateTime = 'updated_at';

    public function __construct($data = [])
    {
        if ($this->table) {
            $this->table = Db::getConfig('prefix').$this->table;
        }
        parent::__construct($data);
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
