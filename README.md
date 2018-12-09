<h1 align="center">
  Tadmin
</h1>

<p align="center">
  <strong>一个基于ThinkPHP5.1+和AmazeUI的快速后台开发框架</strong>
</p>

<p align="center">
  <a href="https://styleci.io/repos/161045623">
    <img src="https://styleci.io/repos/161045623/shield?branch=master" alt="StyleCI">
  </a>   
   <a href="https://packagist.org/packages/techone/tadmin">
      <img src="https://poser.pugx.org/techone/tadmin/v/stable.png" alt="Latest Stable Version">
  </a>   
  <a href="https://packagist.org/packages/techone/tadmin">
      <img src="https://poser.pugx.org/techone/tadmin/downloads.png" alt="Total Downloads">
  </a>   
  <a href="https://packagist.org/packages/techone/tadmin">
    <img src="https://poser.pugx.org/techone/tadmin/license.png" alt="License">
  </a>
</p>

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

## 协议

`Tadmin` 采用 [MIT](LICENSE) 开源协议发布。

## 联系

有问题或者功能建议，请联系我们或者提交PR:
- https://github.com/techoner/tadmin/issues
- techlee@qq.com