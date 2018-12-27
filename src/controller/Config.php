<?php

namespace tadmin\controller;

use tadmin\model\Config as ConfigModel;
use tadmin\support\controller\Controller;
use think\Request;

class Config extends Controller
{
    protected $config;

    public function __construct(ConfigModel $config)
    {
        parent::__construct();
        $this->config = $config;
    }

    public function index()
    {
        $configs = $this->config->select();

        return $this->fetch('config/index', [
            'configs' => $configs,
        ]);
    }

    public function save(Request $request)
    {
        try {
            $this->config->saveAll(array_values($request->post('config/a')));
        } catch (\Exception $e) {
            return $this->error('保存失败');
        }
        $this->redirect('tadmin.config');
    }

    public function add()
    {
        return $this->fetch('config/add');
    }

    public function create(Request $request)
    {
        try {
            $this->config->create($request->post());
        } catch (\Exception $e) {
            return $this->error('保存失败');
        }
        $this->redirect('tadmin.config');
    }
}
