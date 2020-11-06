<?php

namespace tadmin\model;

use think\model\Pivot;
use think\facade\Db;

class ArticleTag extends Pivot
{
    protected $name = 'articles_tags';

    public function __construct($data = [], $parent = null, $table = '')
    {
        $this->parent = $parent;

        if (is_null($this->name)) {
            $this->name = $table;
        }

        parent::__construct($data, $parent, $table);

        if ($this->table) {
            $this->table = Db::getConfig('prefix') . $this->table;
        }
    }
}
