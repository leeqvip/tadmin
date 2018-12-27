<?php

namespace tadmin\controller;

use tadmin\model\Article;
use tadmin\model\OperationLog;
use tadmin\support\controller\Controller;

class Index extends Controller
{
    protected $operationLog;

    protected $article;

    public function __construct(OperationLog $operationLog, Article $article)
    {
        parent::__construct();
        $this->operationLog = $operationLog;
        $this->article = $article;
    }

    public function index(OperationLog $operationLog)
    {
        return $this->fetch('index/index', [
            'logs' => $this->logs(),
            'systemInfo' => $this->systemInfo(),
            'latestRelease' => $this->latestRelease(),
        ]);
    }

    protected function logs()
    {
        return $this->operationLog->with('adminer')->order('id', 'desc')->limit(4)->select();
    }

    protected function systemInfo()
    {
        return [
            'appVersion' => '0.1.0',
            'os' => PHP_OS,
            'serverSoftware' => request()->server('SERVER_SOFTWARE'),
            'phpVersion' => 'PHP '.PHP_VERSION,
            'systemDate' => date('Y年m月d日 H时i分s秒').' ('.date_default_timezone_get().')',
        ];
    }

    protected function latestRelease()
    {
        return $this->article->with('category')->order('id', 'desc')->limit(6)->select();
    }
}
