<?php

namespace tadmin\controller\auth;

use tadmin\model\OperationLog;
use tadmin\support\controller\AbstractController;

class Log extends AbstractController
{
    protected $operationLog;

    public function __construct(OperationLog $operationLog)
    {
        parent::__construct();
        $this->operationLog = $operationLog;
    }

    public function index()
    {
        $logs = $this->operationLog->order('id', 'desc')->paginate();

        return $this->fetch('auth/log/index', [
            'logs' => $logs,
        ]);
    }
}
