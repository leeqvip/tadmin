<?php

namespace tadmin\model;

class Tag extends Model
{
    protected $name = 'tags';

    public function articles()
    {
        return $this->belongsToMany(Article::class, ArticleTag::class, 'article_id', 'tag_id');
    }
}
