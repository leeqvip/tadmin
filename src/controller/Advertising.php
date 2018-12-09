<?php

namespace tadmin\controller;

use tadmin\model\Advertising as AdvertisingModel;
use tadmin\model\AdvertisingBlock;
use tadmin\service\upload\contract\Factory as Uploader;
use tadmin\support\controller\AbstractController;
use think\Request;

class Advertising extends AbstractController
{
    protected $model;

    public function __construct(AdvertisingModel $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index(Request $request, AdvertisingBlock $adBlock)
    {
        $data = $request->only(['block' => [], 'keywords' => '']);

        $advertisings = $this->model
            ->when($data['keywords'], function ($query) use ($data) {
                $query->whereLike('title', '%'.$data['keywords'].'%');
            })
            ->when($data['block'], function ($query) use ($data) {
                $query->whereIn('block', $data['block']);
            })
            ->order('id', 'desc')
            ->with('advertisingBlock')
            ->paginate([
                'query' => $data,
            ]);
        $adBlocks = $adBlock->select();

        return $this->fetch('advertising/index', [
            'advertisings' => $advertisings,
            'adBlocks' => $adBlocks,
        ]);
    }

    public function edit(Request $request, AdvertisingBlock $adBlock)
    {
        $advertising = $this->model->find($request->get('id', 0));
        $adBlocks = $adBlock->select();

        return $this->fetch('advertising/edit', [
            'advertising' => $advertising,
            'adBlocks' => $adBlocks,
        ]);
    }

    public function save(Request $request, Uploader $uploader)
    {
        try {
            $data = $request->post();
            if ($image = $uploader->image('image')) {
                $data['image'] = $image->getUrlPath();
            }

            $this->model->isUpdate($request->get('id') > 0)->save($data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        $this->redirect('tadmin.advertising');
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
