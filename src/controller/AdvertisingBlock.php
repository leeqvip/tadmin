<?php

namespace tadmin\controller;

use tadmin\model\AdvertisingBlock as AdvertisingBlockModel;
use tadmin\support\controller\Controller;
use think\Request;

class AdvertisingBlock extends Controller
{
    protected $model;

    public function __construct(AdvertisingBlockModel $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $blocks = $this->model->paginate();

        return $this->fetch('advertising/block/index', [
            'blocks' => $blocks,
        ]);
    }

    public function edit(Request $request)
    {
        $block = $this->model->findOrEmpty($request->get('id', 0));

        return $this->fetch('advertising/block/edit', [
            'block' => $block,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();

            $this->model->updateOrCreate(['id' => $request->get('id', 0)], $data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        return $this->redirect('tadmin.advertising.block');
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
