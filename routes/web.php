<?php

//验证码
Route::get('/captcha/{group}', 'CaptchaController@captcha')->name('captcha');

//网站首页
Route::get('/', 'IndexController@index')->name('index');

//第一层（设置命令空间和前缀）
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {

    $this->get('logout', 'Auth\LoginController@logout')->name('admin.logout');

    //第二层（设置未登录中间件）
    Route::group(['middleware' => 'login_guest'], function () {
        $this->get('login', 'Auth\LoginController@showLoginForm')->name('admin.login');
        $this->post('login', 'Auth\LoginController@login');
    });

    //第二层（设置登录中间件）
    Route::group(['middleware' => 'login_auth'], function () {

        // ---------------------------后台首页--------------------------- //
        Route::get('/', 'IndexController@index')->name('admin');

        // ---------------------------组长及以上权限操作--------------------------- //
        Route::group(['middleware' => 'manage'], function () {
            Route::get('/salesman/list/', 'SalesmanController@listView')->name('salesman_list');
            Route::get('/salesman/list/{keyword}', 'SalesmanController@listView')->name('salesman_search');
            Route::get('/salesman/add', 'SalesmanController@addView')->name('salesman_add');
            Route::post('/salesman/add', 'SalesmanController@post');
            Route::get('/salesman/update/{id}', 'SalesmanController@updateView')->name('salesman_update');
            Route::post('/salesman/update/{id}', 'SalesmanController@post');
            Route::get('/salesman/destroy/{id}', 'SalesmanController@destroy')->name('salesman_destroy');

            //修改客户回访信息
            Route::get('/visit/update/{id}', 'VisitController@updateView')->name('visit_update');
            Route::post('/visit/update/{id}', 'VisitController@post');

            //删除客户
            Route::get('/visit/destroy/{id}', 'VisitController@destroy')->name('visit_destroy');

            //根据业务员查看客户
            Route::get('/customer/list/salemans/{salesman}', 'CustomerController@listBySalesman')->name('customer_by_salesman');
        });

        // ---------------------------超级管理操作--------------------------- //
        Route::group(['middleware' => 'admin'], function () {
            //消息相关
            Route::get('/message/list/', 'MessageController@listView')->name('message_list');
            Route::get('/message/list/{keyword}', 'MessageController@listView')->name('message_search');
            Route::get('/message/update/{id}', 'MessageController@update')->name('message_update');

            //分组相关
            Route::get('/group/list/', 'GroupController@listView')->name('group_list');
            Route::get('/group/list/{keyword}', 'GroupController@listView')->name('group_search');
            Route::get('/group/add', 'GroupController@addView')->name('group_add');
            Route::post('/group/add', 'GroupController@post');
            Route::get('/group/update/{id}', 'GroupController@updateView')->name('group_update');
            Route::post('/group/update/{id}', 'GroupController@post');
            Route::get('/group/destroy/{id}', 'GroupController@destroy')->name('group_destroy');

            //根据分组查看客户
            Route::get('/customer/list/group/{group}', 'CustomerController@listByGroup')->name('customer_by_group');
        });

        // ---------------------------业务员操作--------------------------- //
        //客户列表
        Route::get('/customer/list/', 'CustomerController@listView')->name('customer_list');

        //搜索
        Route::get('/customer/list/{keyword}', 'CustomerController@listView')->name('customer_search');

        //添加客户
        Route::get('/customer/add', 'CustomerController@addView')->name('customer_add');
        Route::post('/customer/add', 'CustomerController@post');

        //修改客户信息
        Route::get('/customer/update/{id}', 'CustomerController@updateView')->name('customer_update');
        Route::post('/customer/update/{id}', 'CustomerController@post');

        //删除客户
        Route::get('/customer/destroy/{id}', 'CustomerController@destroy')->name('customer_destroy');

        //客户回访记录列表
        Route::get('/visit/list/', 'VisitController@listView')->name('visit_list');
        Route::get('/visit/list/{keyword}', 'VisitController@listView')->name('visit_search');

        //添加客户回访记录
        Route::get('/visit/add', 'VisitController@addView')->name('visit_add');
        Route::post('/visit/add', 'VisitController@post');
    });
});