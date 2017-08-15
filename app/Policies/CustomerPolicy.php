<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return bool
     */
    public function __construct()
    {
        
    }

    /**
     * 判断超级管理员
     *
     * @param $user
     * @return bool
     */
    public function admin($user)
    {
        return $user['name'] === config('site.admin_name');
    }

    /**
     * 是否可以控制当前客户记录
     * 允许管理员跨过权限操作
     *
     * @param $user
     * @param $customer
     * @return bool
     */
    public function control($user, $customer)
    {
        if ($this->admin($user)) {
            return true;
        }

        return $user['id'] === $customer['salesman_id'];
    }
}
