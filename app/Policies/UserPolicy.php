<?php

namespace App\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    public function admin($user)
    {
        return $user['name'] === config('site.admin_name');
    }

    public function view($user)
    {
        return $user['name'] === config('site.admin_name');
    }

    public function manage($user)
    {
        return $user['type'] == 1 || $user['type'] == 2;
    }

    /**
     * 是否可以控制当前客户记录
     * 允许管理员跨过权限操作
     *
     * @param $user
     * @param $customer
     * @return bool
     */
    public function control($user, $salesman)
    {
        //跳过超级管理员
        if ($this->admin($user)) {
            return true;
        }

        //自己的业务员
        if ($user['id'] == $salesman['parent_id']) {
            return true;
        }

        return false;
    }
}
