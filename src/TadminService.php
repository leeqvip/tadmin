<?php

namespace tadmin;

use think\Service;
use think\Route;
use Casbin\Model\Model;
use Casbin\Enforcer;

class TadminService extends Service
{
    /**
     * 路由分组名.
     *
     * @var string
     */
    protected $name = 'tadmin';

    /**
     * 控制器命名空间.
     *
     * @var string
     */
    protected $namespace = '\\tadmin\\controller\\';

    public function register()
    {
        $this->bindProviders();

        // 绑定 Casbin决策器
        $this->app->bind('tadmin.enforcer', function () {
            $config = $this->app->config->get('tadmin.enforcer');
            $adapter = $config['adapter'];
            $model = new Model();
            $model->loadModel($config['model_config_path']);
            return new Enforcer($model, app($adapter), false);
        });
    }

    public function boot(Route $route)
    {
        $this->loadConfig();
        $this->bootRoute($route);
    }

    protected function loadConfig()
    {
        $configFileNames = [
            'console',
            'middleware',
            'casbin',
            'tadmin',
        ];
        foreach ($configFileNames as $fileName) {
            if (is_file(admin_config_path($fileName . '.php'))) {
                $file = admin_config_path($fileName . '.php');
                $configName = pathinfo($file, PATHINFO_FILENAME);
                $config = $this->app->config->get($configName);

                $config = array_deep_merge(
                    require_once $file,
                    $config
                );

                $this->app->config->set($config, $configName);
            }
        }
    }

    protected function bindProviders()
    {
        if (is_file(admin_config_path('provider.php'))) {
            $this->app->bind(
                include_once admin_config_path('/provider.php')
            );
        }
    }

    protected function bootRoute(Route $route)
    {
        $routePath = admin_route_path();
        // 路由检测
        $files = scandir($routePath);
        foreach ($files as $file) {
            if (strpos($file, '.php')) {
                $filename = $routePath . $file;
                // 导入路由配置
                $route->group($this->name, function () use ($filename) {
                    include_once($filename);
                })
                    ->prefix($this->namespace)
                    ->middleware(\think\middleware\SessionInit::class);
            }
        }
    }
}
