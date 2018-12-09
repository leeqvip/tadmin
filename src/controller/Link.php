<?php

namespace tadmin\controller;

use tadmin\model\Link as LinkModel;
use tadmin\model\LinkBlock;
use tadmin\service\upload\contract\Factory as Uploader;
use tadmin\support\controller\AbstractController;
use think\Request;

class Link extends AbstractController
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
        $link = $this->model->find($request->get('id', 0));
        $linkBlocks = $linkBlock->select();

        return $this->fetch('link/edit', [
            'link' => $link,
            'linkBlocks' => $linkBlocks,
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
        $this->redirect('tadmin.link');
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
