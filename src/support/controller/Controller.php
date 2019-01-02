<?php

namespace tadmin\support\controller;

use tadmin\model\Menu;
use tadmin\service\auth\facade\Auth;
use think\Container;
use think\Controller as ThinkController;
use think\facade\Config;
use think\Console;
use think\Loader;

abstract class Controller extends ThinkController
{
    protected $viewPath = '';

    public function __construct()
    {
        parent::__construct();

        $this->initConfig();

        $this->setViewPath();

        $this->assignCommon();
    }

    public function initConfig()
    {
        if (is_file(admin_config_path('paginate.php'))) {
            $paginateAdmin = include admin_config_path('paginate.php');
            $config = Container::get('config');
            $paginate = $config->pull('paginate');
            $config->set(array_merge(
                \is_array($paginate) ? $paginate : [],
                $paginateAdmin
            ), 'paginate');
        }
    }

    public function setViewPath()
    {
        $this->viewPath = config('tadmin.template.view_path');
        $this->view->config('view_path', $this->viewPath);
        $this->view->config('tpl_replace_string', config('tadmin.template.tpl_replace_string'));
        $assets = ltrim(config('tadmin.template.tpl_replace_string.__TADMIN_ASSETS__'), '/');
        $publicName = trim(config('tadmin.template.public_name'), '/');
        $documentPath = Loader::getRootPath();

        if (!empty($publicName)) {
            $documentPath .= $publicName.'/';
        }

        if (!file_exists($documentPath.$assets)) {
            throw new \Exception('Resource not published,Please initialize tadmin.');
            // Console::call('tadmin:init');
        }
    }

    public function assignCommon()
    {
        $menus = app(Menu::class)->toTree();
        $adminer = Auth::user();
        $this->view->assign(compact('menus', 'adminer'));
    }
}
