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

        // ---------------------------管理员管理业务员（必须管理员才可以操作）--------------------------- //
        Route::group(['middleware' => 'admin'], function () {
            Route::get('/salesman/list', function () {
                return redirect()->route('salesman_list', ['page' => 1]);
            })->name('salesman_list_simple');
            Route::get('/salesman/list/{page}', 'SalesmanController@listView')->name('salesman_list');
            Route::get('/salesman/add', 'SalesmanController@addView')->name('salesman_add');
            Route::post('/salesman/add', 'SalesmanController@post');
            Route::get('/salesman/update/{id}', 'SalesmanController@updateView')->name('salesman_update');
            Route::post('/salesman/update/{id}', 'SalesmanController@post');
            Route::get('/salesman/destroy/{id}', 'SalesmanController@destroy')->name('salesman_destroy');

            //搜索
            Route::get('/salesman/search', function () {
                return redirect()->route('salesman_search', ['page' => 1, 'keyword' => '']);
            })->name('salesman_search_simple');
            Route::get('/salesman/search/{page}/{keyword}', 'SalesmanController@search')->name('salesman_search');

            //修改客户回访信息
            Route::get('/visit/update/{id}', 'VisitController@updateView')->name('visit_update');
            Route::post('/visit/update/{id}', 'VisitController@post');

            //删除客户
            Route::get('/visit/destroy/{id}', 'VisitController@destroy')->name('visit_destroy');
        });

        // ---------------------------业务员操作--------------------------- //
        //客户列表
        Route::get('/customer/list', function () {
            return redirect()->route('customer_list', ['page' => 1]);
        })->name('customer_list_simple');
        Route::get('/customer/list/{page}', 'CustomerController@listView')->name('customer_list');

        //添加客户
        Route::get('/customer/add', 'CustomerController@addView')->name('customer_add');
        Route::post('/customer/add', 'CustomerController@post');

        //修改客户信息
        Route::get('/customer/update/{id}', 'CustomerController@updateView')->name('customer_update');
        Route::post('/customer/update/{id}', 'CustomerController@post');

        //删除客户
        Route::get('/customer/destroy/{id}', 'CustomerController@destroy')->name('customer_destroy');

        //搜索客户
        Route::get('/customer/search', function () {
            return redirect()->route('customer_search', ['page' => 1, 'keyword' => '']);
        })->name('customer_search_simple');
        Route::get('/customer/search/{page}/{keyword}', 'CustomerController@search')->name('customer_search');

        //客户回访记录列表
        Route::get('/visit/list', function () {
            return redirect()->route('visit_list', ['page' => 1]);
        })->name('visit_list_simple');
        Route::get('/visit/list/{page}', 'VisitController@listView')->name('visit_list');

        //添加客户回访记录
        Route::get('/visit/add', 'VisitController@addView')->name('visit_add');
        Route::post('/visit/add', 'VisitController@post');

        //搜索客户回访记录
        Route::get('/visit/search', function () {
            return redirect()->route('visit_search', ['page' => 1, 'keyword' => '']);
        })->name('visit_search_simple');
        Route::get('/visit/search/{page}/{keyword}', 'VisitController@search')->name('visit_search');
    });
});