<?php

namespace tadmin\model;

class Article extends Model
{
    protected $name = 'articles';

    protected $append = ['summary_text'];

    public function getSummaryTextAttr()
    {
        $summary = $this->getAttr('summary');
        if (empty($summary)) {
            $summary = strip_tags($this->getAttr('content'));
        }

        return $summary;
    }

    //上一篇
    public function previous()
    {
        $prev = $this->where('sort', 'elt', $this->sort)
            ->field('id,title')
            ->where('updated_at', 'elt', $this->updated_at)
            ->where('id', 'lt', $this->id)
            ->where('category_id', 'eq', $this->category_id)
            ->order(['sort' => 'asc', 'updated_at' => 'desc', 'id' => 'desc'])
            ->find();

        return $prev;
    }

    public function next()
    {
        $next = $this->where('sort', 'egt', $this->sort)
            ->field('id,title')
            ->where('updated_at', 'egt', $this->updated_at)
            ->where('id', 'gt', $this->id)
            ->where('category_id', 'eq', $this->category_id)
            ->order(['sort' => 'desc', 'updated_at' => 'asc', 'id' => 'asc'])
            ->find();

        return $next;
    }

    public function category()
    {
        return $this->belongsTo(Category::class, "category_id");
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, ArticleTag::class, 'tag_id', 'article_id');
    }
}
