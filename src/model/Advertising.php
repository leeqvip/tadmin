<?php

namespace tadmin\model;

class Advertising extends Model
{
    protected $table = 'advertisings';

    public function advertisingBlock()
    {
        return $this->belongsTo(AdvertisingBlock::class, 'block', 'block');
    }
}
