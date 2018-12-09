<?php

Route::group('auth', function () {
    Route::get('/passport/login', 'auth\\Passport@login')->name('tadmin.auth.passport.login');
    Route::post('/passport/login', 'auth\\Passport@loginAuth');

    Route::get('/passport/logout', 'auth\\Passport@logout')->name('tadmin.auth.passport.logout')->middleware('tadmin.admin');
    Route::get('/passport/user', 'auth\\Passport@user')->name('tadmin.auth.passport.user')->middleware('tadmin.admin');
});

Route::group([
    'middleware' => ['tadmin.admin'],
], function () {
    Route::group('auth', function () {
        // 管理员
        Route::get('/adminer/delete', 'auth\\Adminer@delete')->name('tadmin.auth.adminer.delete');
        Route::get('/adminer/edit', 'auth\\Adminer@edit')->name('tadmin.auth.adminer.edit');
        Route::post('/adminer/edit', 'auth\\Adminer@save');
        Route::get('/adminer', 'auth\\Adminer@index')->name('tadmin.auth.adminer');

        // 角色
        Route::get('/role/delete', 'auth\\Role@delete')->name('tadmin.auth.role.delete');
        Route::get('/role/edit', 'auth\\Role@edit')->name('tadmin.auth.role.edit');
        Route::post('/role/edit', 'auth\\Role@save');
        Route::get('/role', 'auth\\Role@index')->name('tadmin.auth.role');

        // 权限
        Route::get('/permission/delete', 'auth\\Permission@delete')->name('tadmin.auth.permission.delete');
        Route::get('/permission/edit', 'auth\\Permission@edit')->name('tadmin.auth.permission.edit');
        Route::post('/permission/edit', 'auth\\Permission@save');
        Route::get('/permission', 'auth\\Permission@index')->name('tadmin.auth.permission');

        Route::get('/log', 'auth\\Log@index')->name('tadmin.auth.log');
    });

    // 首页
    Route::get('/', 'Index@index')->name('tadmin.index');
    Route::get('/dashboard', 'Index@index');

    Route::get('/config/add', 'Config@add')->name('tadmin.config.add');
    Route::post('/config/add', 'Config@create');
    Route::get('/config', 'Config@index')->name('tadmin.config');
    Route::post('/config', 'Config@save');

    // 分类
    Route::get('/category/delete', 'Category@delete')->name('tadmin.category.delete');
    Route::get('/category/edit', 'Category@edit')->name('tadmin.category.edit');
    Route::post('/category/edit', 'Category@save');
    Route::get('/category', 'Category@index')->name('tadmin.category');

    // 文章
    Route::get('/article/delete', 'Article@delete')->name('tadmin.article.delete');
    Route::get('/article/edit', 'Article@edit')->name('tadmin.article.edit');
    Route::post('/article/edit', 'Article@save');
    Route::get('/article', 'Article@index')->name('tadmin.article');

    // 单页
    Route::get('/single/delete', 'Single@delete')->name('tadmin.single.delete');
    Route::get('/single/edit', 'Single@edit')->name('tadmin.single.edit');
    Route::post('/single/edit', 'Single@save');
    Route::get('/single', 'Single@index')->name('tadmin.single');

    // 导航菜单
    Route::get('/nav/delete', 'Nav@delete')->name('tadmin.nav.delete');
    Route::get('/nav/edit', 'Nav@edit')->name('tadmin.nav.edit');
    Route::post('/nav/edit', 'Nav@save');
    Route::get('/nav', 'Nav@index')->name('tadmin.nav');

    // 广告管理
    Route::get('/advertising/block/delete', 'AdvertisingBlock@delete')->name('tadmin.advertising.block.delete');
    Route::get('/advertising/block/edit', 'AdvertisingBlock@edit')->name('tadmin.advertising.block.edit');
    Route::post('/advertising/block/edit', 'AdvertisingBlock@save');
    Route::get('/advertising/block', 'AdvertisingBlock@index')->name('tadmin.advertising.block');

    Route::get('/advertising/delete', 'Advertising@delete')->name('tadmin.advertising.delete');
    Route::get('/advertising/edit', 'Advertising@edit')->name('tadmin.advertising.edit');
    Route::post('/advertising/edit', 'Advertising@save');
    Route::get('/advertising', 'Advertising@index')->name('tadmin.advertising');

    // 链接管理
    Route::get('/link/block/delete', 'LinkBlock@delete')->name('tadmin.link.block.delete');
    Route::get('/link/block/edit', 'LinkBlock@edit')->name('tadmin.link.block.edit');
    Route::post('/link/block/edit', 'LinkBlock@save');
    Route::get('/link/block', 'LinkBlock@index')->name('tadmin.link.block');

    Route::get('/link/delete', 'Link@delete')->name('tadmin.link.delete');
    Route::get('/link/edit', 'Link@edit')->name('tadmin.link.edit');
    Route::post('/link/edit', 'Link@save');
    Route::get('/link', 'Link@index')->name('tadmin.link');

    // 招聘管理、
    Route::get('/job/resume/item', 'Job@resumeItem')->name('tadmin.job.resume.item');
    Route::get('/job/resume', 'Job@resume')->name('tadmin.job.resume');
    Route::get('/job/delete', 'Job@delete')->name('tadmin.job.delete');
    Route::get('/job/edit', 'Job@edit')->name('tadmin.job.edit');
    Route::post('/job/edit', 'Job@save');
    Route::get('/job', 'Job@index')->name('tadmin.job');

    Route::get('/message/delete', 'Message@delete')->name('tadmin.message.delete');
    Route::get('/message', 'Message@index')->name('tadmin.message');

    Route::any('/upload/image', 'Upload@image')->name('tadmin.upload.image');
    Route::any('/upload/ueditor', 'Upload@ueditor')->name('tadmin.upload.ueditor');
});
