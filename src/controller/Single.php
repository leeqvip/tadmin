<?php

namespace tadmin\controller;

use tadmin\model\Category;
use tadmin\model\Single as SingleModel;
use tadmin\support\controller\Controller;
use think\Request;

class Single extends Controller
{
    protected $single;

    protected $category;

    public function __construct(SingleModel $single, Category $category)
    {
        parent::__construct();
        $this->single = $single;
        $this->category = $category;
    }

    public function index(Request $request, Category $category)
    {
        $data = $request->only(['category_id' => [], 'keywords' => '']);

        $articles = $this->single
            ->where('type', 1)
            ->when($data['keywords'], function ($query) use ($data) {
                $query->whereLike('title', '%'.$data['keywords'].'%');
            })
            ->when($data['category_id'], function ($query) use ($data) {
                $query->whereIn('category_id', $data['category_id']);
            })
            ->order('id', 'desc')
            ->with('category')
            ->paginate([
                'query' => $data,
            ]);
        $parents = $category->flatTree();

        return $this->fetch('single/index', [
            'articles' => $articles,
            'parents' => $parents,
        ]);
    }

    public function edit(Request $request, Category $category)
    {
        $article = $this->single->findOrEmpty($request->get('id', 0));
        $parents = $category->flatTree();

        return $this->fetch('single/edit', [
            'article' => $article,
            'parents' => $parents,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            if ($image = $this->file($request, 'image')) {
                $data['image'] = $this->uploadImage($image);
            }

            $parent = $this->category->find($data['category_id']);
            if (!$parent) {
                return $this->error('所属栏目不存在');
            }

            $hasBinding = $this->single->where('category_id', $data['category_id'])->when($data['id'], function ($query) use ($data) {
                $query->where('id', '<>', $data['id']);
            })->count();

            if ($hasBinding) {
                throw new \Exception('该栏目已经绑定其他单页');
            }

            $data['category_parent_path'] = isset($parent) ? $parent['parent_path'].$parent['id'].',' : '0,';

            $this->single->updateOrCreate(['id' => $request->get('id', 0)], $data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        
        return $this->redirect('tadmin.single');
    }

    public function delete(Request $request)
    {
        try {
            $this->single->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }
}
