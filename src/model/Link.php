<?php

namespace tadmin\model;

class Link extends Model
{
    protected $table = 'links';

    public function linkBlock()
    {
        return $this->belongsTo(LinkBlock::class, 'block', 'block');
    }
}
