<?php

namespace tadmin\controller;

use tadmin\model\Advertising as AdvertisingModel;
use tadmin\model\AdvertisingBlock;
use tadmin\support\controller\Controller;
use think\Request;

class Advertising extends Controller
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
        $advertising = $this->model->findOrEmpty($request->get('id', 0));
        $adBlocks = $adBlock->select();

        return $this->fetch('advertising/edit', [
            'advertising' => $advertising,
            'adBlocks' => $adBlocks,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $data = $request->post();
            if ($image = $this->file($request, 'image')) {
                $data['image'] = $this->uploadImage($image);
            }

            $this->model->updateOrCreate(['id' => $request->get('id', 0)], $data);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
        
        return $this->redirect('tadmin.advertising');
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
