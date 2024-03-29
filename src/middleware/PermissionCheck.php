<?php

namespace tadmin\middleware;

use tadmin\controller\Transfer;
use tadmin\service\auth\facade\Auth;
use tadmin\support\controller\Controller;
use tauthz\facade\Enforcer;
use think\App;

class PermissionCheck extends Controller
{
    protected $app;

    protected $request;

    public function __construct(App $app)
    {
        parent::__construct($app);
    }

    public function handle($request, \Closure $next)
    {
        $this->request = $request;

        if (!$adminer = Auth::user()) {
            return $next($request);
        }

        if ($this->shouldPassThrough()) {
            return $next($request);
        }

        $enforcer = $this->app->get('tadmin.enforcer');

        if (true !== $enforcer->enforce('adminer.' . $adminer->id, $this->request->method(true), $this->parseCurrentPath())) {
            return $this->error("权限不足");
        }

        return $next($request);
    }

    public function shouldPassThrough()
    {
        $excepts = [
            '/',
            '',
            'dashboard',
        ];

        foreach ($excepts as $except) {
            if ('/' !== $except) {
                $except = trim($except, '/');
            }
            if ($except == $this->parseCurrentPath()) {
                return true;
            }
        }

        return false;
    }

    public function parseCurrentPath()
    {
        $currentPath = ltrim(trim($this->request->baseUrl(), '/'), 'tadmin');
        if ('/' !== $currentPath) {
            $currentPath = rtrim($currentPath, '/');
        }

        return $currentPath;
    }

    public function parseHttpPath($httpPath)
    {
        return array_map(function ($row) {
            return rtrim(trim($row), '/');
        }, explode(PHP_EOL, $httpPath));
    }
}
