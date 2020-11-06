<?php

namespace tadmin\controller;

use tadmin\model\Category as CategoryModel;
use tadmin\support\controller\Controller;
use think\Request;

class Category extends Controller
{
    protected $category;

    public function __construct(CategoryModel $category)
    {
        parent::__construct();
        $this->category = $category;
    }

    public function index()
    {
        $categorys = $this->category->flatTree();

        return $this->fetch('category/index', [
            'categorys' => $categorys,
        ]);
    }

    public function edit(Request $request)
    {
        $category = $this->category->findOrEmpty($request->get('id', 0));

        $parents = $this->category->flatTree();

        return $this->fetch('category/edit', [
            'category' => $category,
            'parents' => $parents,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();

            if (!empty($data['parent_id'])) {
                $parent = $this->category->find($data['parent_id']);
                if (!$parent) {
                    return $this->error('所选上级栏目不存在');
                }
            }

            $data['parent_path'] = isset($parent) ? $parent['parent_path'].$parent['id'].',' : '0,';
            $cate = $this->category->findOrEmpty($data['id']);
            $cate->save($data);
        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        return $this->redirect('tadmin.category');
    }

    public function delete(Request $request)
    {
        try {
            $this->category->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }
}
