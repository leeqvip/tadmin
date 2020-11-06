<?php

namespace tadmin\controller;

use tadmin\model\Link as LinkModel;
use tadmin\model\LinkBlock;
use tadmin\support\controller\Controller;
use think\Request;

class Link extends Controller
{
    protected $model;

    public function __construct(LinkModel $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index(Request $request, LinkBlock $linkBlock)
    {
        $data = $request->only(['block' => [], 'keywords' => '']);

        $links = $this->model
            ->when($data['keywords'], function ($query) use ($data) {
                $query->whereLike('title', '%'.$data['keywords'].'%');
            })
            ->when($data['block'], function ($query) use ($data) {
                $query->whereIn('block', $data['block']);
            })
            ->order('id', 'desc')
            ->with('linkBlock')
            ->paginate([
                'query' => $data,
            ]);
        $linkBlocks = $linkBlock->select();

        return $this->fetch('link/index', [
            'links' => $links,
            'linkBlocks' => $linkBlocks,
        ]);
    }

    public function edit(Request $request, LinkBlock $linkBlock)
    {
        $link = $this->model->findOrEmpty($request->get('id', 0));
        $linkBlocks = $linkBlock->select();

        return $this->fetch('link/edit', [
            'link' => $link,
            'linkBlocks' => $linkBlocks,
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

        return $this->redirect('tadmin.link');
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
