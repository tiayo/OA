<?php

namespace App\Policies;

use App\Repositories\UsersRepository;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class CustomerPolicy
{
    use HandlesAuthorization;

    protected $salesman;

    public function __construct(UsersRepository $salesman)
    {
        $this->salesman = $salesman;
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
        //跳过超级管理员
        if ($this->admin($user)) {
            return true;
        }

        //自己的客户
        if ($user['id'] == $customer['salesman_id']) {
            return true;
        }

        //负责人鉴权
        $all_children = $this->salesman->getChildren(Auth::id(), 'id', 'parent_id');

        foreach ($all_children as $child) {
            if ($customer['salesman_id'] == $child['id']) {
                return true;
            }
        }

        return false;
    }
}
