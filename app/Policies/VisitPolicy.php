<?php

namespace App\Policies;

use App\Repositories\UsersRepository;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

class VisitPolicy
{
    use HandlesAuthorization;

    protected $salesman;

    /**
     * Create a new policy instance.
     *
     * @return bool
     */
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
        return CustomerPolicy::admin($user);
    }

    /**
     * 是否可以控制当前记录
     * 允许管理员跨过权限操作
     *
     * @param $user
     * @param $visit
     * @return bool
     */
    public function control($user, $visit)
    {
        //管理员跳过
        if ($this->admin($user)) {
            return true;
        }

        //带代理级别以上拒绝
        if (!Auth::user()->can('manage', User::class)) {
            return false;
        }

        //自己的客户记录
        if ($user['id'] === $visit['salesman_id']) {
            return true;
        }

        //负责人鉴权
        $all_children = $this->salesman->getChildren(Auth::id(), 'id', 'parent_id');

        foreach ($all_children as $child) {
            if ($visit['salesman_id'] == $child['id']) {
                return true;
            }
        }

        return false;
    }
}
