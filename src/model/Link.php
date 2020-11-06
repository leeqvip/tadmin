<?php

namespace tadmin\model;

class Link extends Model
{
    protected $name = 'links';

    public function linkBlock()
    {
        return $this->belongsTo(LinkBlock::class, 'block', 'block');
    }
}
