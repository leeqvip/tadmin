<?php

namespace tadmin\model;

class Advertising extends Model
{
    protected $name = 'advertisings';

    public function advertisingBlock()
    {
        return $this->belongsTo(AdvertisingBlock::class, 'block', 'block');
    }
}
