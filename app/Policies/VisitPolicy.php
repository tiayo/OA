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
        //跳过超级管理员
        if ($this->admin($user)) {
            return true;
        }

        //自己的客户
        if ($user['id'] == $visit['salesman_id']) {
            return true;
        }

        //负责人鉴权
        if (!can('manage')) {
            return false;
        }

        //获取组成员
        return $this->salesman->selectFirst([
            ['group', $user['group']],
            ['id', $visit['salesman_id']]
        ], 'id')['id'] ? true : false;
    }
}
