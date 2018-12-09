<?php

namespace tadmin\middleware;

use tadmin\service\auth\facade\Auth;
use Casbin;

class PermissionCheck
{
    protected $request;

    public function handle($request, \Closure $next)
    {
        $this->request = $request;

        if (!$adminer = Auth::user()) {
            return $next($request);
        }

        if ($this->shouldPassThrough()) {
            return $next($request);
        }

        if (true !== Casbin::enforce($this->request->method(true), $this->parseCurrentPath())) {
            throw new \Exception('权限不足');
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
        $currentPath = ltrim(trim($this->request->path(), '/'), 'tadmin');
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
