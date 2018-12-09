<?php

namespace tadmin\service\upload;

use TechOne\Support\Manager;
use think\App;

class Uploader extends Manager implements contract\Factory
{
    protected $app;

    protected $filesystems = [
        'default' => '',
        'disks' => '',
    ];

    public function __construct(App $app)
    {
        $this->app = $app;

        $this->filesystems = array_merge(
            $this->filesystems,
            $this->app->config->pull('filesystems')
        );
    }

    public function getDefaultDriver()
    {
        return $this->filesystems['default'];
    }

    public function createLocalDriver()
    {
        if (!isset($this->filesystems['disks'][$this->filesystems['default']])) {
            throw new \Exception('文件存储驱动不存在');
        }

        return app(driver\Local::class, [$this->filesystems['disks'][$this->filesystems['default']]]);
    }
}
