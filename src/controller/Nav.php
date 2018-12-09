<?php

namespace tadmin\controller;

use tadmin\model\Nav as NavModel;
use tadmin\support\controller\AbstractController;
use think\Request;

class Nav extends AbstractController
{
    protected $model;

    public function __construct(NavModel $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index()
    {
        $navs = $this->model->flatTree();

        return $this->fetch('nav/index', [
            'navs' => $navs,
        ]);
    }

    public function edit(Request $request)
    {
        $nav = $this->model->find($request->get('id', 0));

        $parents = $this->model->flatTree();

        $targets = $this->model::mapTarget();

        return $this->fetch('nav/edit', [
            'nav' => $nav,
            'parents' => $parents,
            'targets' => $targets,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();

            $res = $this->model->isUpdate($request->get('id') > 0)->save($data);
        } catch (\Exception $e) {
            $this->error('保存失败');
        }
        $this->redirect('tadmin.nav');
    }

    public function delete(Request $request)
    {
        try {
            $this->model->destroy($request->get('id'));
        } catch (\Exception $e) {
            return $this->error('删除失败');
        }

        return $this->success('删除成功');
    }
}
