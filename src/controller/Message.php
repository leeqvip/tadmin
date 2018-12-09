<?php

namespace tadmin\controller;

use tadmin\model\MessageBoard;
use tadmin\support\controller\AbstractController;
use think\Request;

class Message extends AbstractController
{
    protected $model;

    public function __construct(MessageBoard $model)
    {
        parent::__construct();
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $data = $request->only(['keywords' => '']);

        $messages = $this->model
            ->when($data['keywords'], function ($query) use ($data) {
                $query->whereLike('title', '%'.$data['keywords'].'%');
            })
            ->order('id', 'desc')
            ->paginate([
                'query' => $data,
            ]);

        return $this->fetch('message/index', [
            'messages' => $messages,
        ]);
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
