<?php

namespace tadmin\controller;

use tadmin\model\Article as ArticleModel;
use tadmin\model\ArticleTag;
use tadmin\model\Category;
use tadmin\model\Tag;
use tadmin\service\upload\contract\Factory as Uploader;
use tadmin\support\controller\Controller;
use think\Request;

class Article extends Controller
{
    protected $article;

    protected $category;

    public function __construct(ArticleModel $article, Category $category)
    {
        parent::__construct();
        $this->article = $article;
        $this->category = $category;
    }

    public function index(Request $request)
    {
        $data = $request->only(['category_id' => [], 'keywords' => '']);

        $articles = $this->article
            ->where('type', 1)
            ->when($data['keywords'], function ($query) use ($data) {
                $query->whereLike('title', '%'.$data['keywords'].'%');
            })
            ->when($data['category_id'], function ($query) use ($data) {
                $query->whereIn('category_id', $data['category_id']);
                foreach ($data['category_id'] as $value) {
                    $query->whereOr('category_parent_path', 'like', '%,'.$value.',%');
                }
            })
            ->order('id', 'desc')
            ->with('category')
            ->paginate([
                'query' => $data,
            ]);
        $parents = $this->category->flatTree();

        return $this->fetch('article/index', [
            'articles' => $articles,
            'parents' => $parents,
        ]);
    }

    public function edit(Request $request, Tag $tag)
    {
        $article = $this->article->find($request->get('id', 0));
        $parents = $this->category->flatTree();
        $tags = $tag->select();

        return $this->fetch('article/edit', [
            'article' => $article,
            'parents' => $parents,
            'tags' => $tags,
        ]);
    }

    public function save(Request $request, Uploader $uploader)
    {
        try {
            $data = $request->post();
            if ($image = $uploader->image('image')) {
                $data['image'] = $image->getUrlPath();
            }

            if (!empty($data['category_id'])) {
                $parent = $this->category->find($data['category_id']);
                if (!$parent) {
                    return $this->error('所属栏目不存在');
                }
            }

            $data['category_parent_path'] = isset($parent) ? $parent['parent_path'].$parent['id'].',' : '0,';

            $tags = isset($data['tags']) ? $data['tags'] : [];

            $article = $this->article->updateOrCreate(['id' => $request->get('id', 0)], $data);

            foreach ($tags as $tag) {
                ArticleTag::create([
                    'article_id' => $article->id,
                    'tag_id' => $tag,
                ], true, true);
            }
            ArticleTag::where('article_id', $article->id)->whereNotIn('tag_id', $tags)->delete();
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->redirect('tadmin.article');
    }

    public function delete(Request $request)
    {
        try {
            $this->article->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }
}
