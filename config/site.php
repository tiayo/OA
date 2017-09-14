<?php

return [
    'admin_name' => explode(',', env('ADMIN_NAME', 'admin')), //超级管理员用户名
    'list_num' => env('LIST_NUM', 15), //每页显示条数
    'title' => '星科技客户关系管理系统', //网站标题
];