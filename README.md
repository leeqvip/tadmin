Tadmin
=====

[![StyleCI](https://github.styleci.io/repos/149270146/shield?branch=master)](https://github.styleci.io/repos/149270146)
[![Latest Stable Version](https://poser.pugx.org/techone/admin/v/stable)](https://packagist.org/packages/techone/admin)
[![Total Downloads](https://poser.pugx.org/techone/admin/downloads)](https://packagist.org/packages/techone/admin)
[![License](https://poser.pugx.org/techone/admin/license)](https://packagist.org/packages/techone/admin)

**Tadmin** 是一个基于ThinkPHP5.1+和AmazeUI的快速后台开发框架。

## 安装

最方便的安装方式就是使用Composer ( https://getcomposer.org/ )，在这之前务必先搭建好thinkphp5.1项目

1、安装 Tadmin :

```
composer require techone/tadmin
```

2、初始化和数据迁移

```
php think tadmin:init
php think tadmin:migrate:run
```

3、配置

添加行为在 `application/tags.php`

```
return [

    'app_init'     => [
        \tadmin\behavior\Boot::class,
    ],

    // ...
];
```

## 进入tadmin后台

打开后台地址，例如：

http://yourdomain/tadmin
