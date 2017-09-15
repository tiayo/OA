<?php

namespace App\Policies;

use App\Repositories\GroupRepository;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    protected $group;

    public function __construct(GroupRepository $group)
    {
        $this->group = $group;
    }

    public function admin($user)
    {
        return CustomerPolicy::admin($user);
    }

    public function view($user)
    {
        return $user['name'] === config('site.admin_name');
    }

    public function manage($user)
    {
        return $user['type'] == 1 || $user['type'] == 2;
    }

    public function user($user)
    {
        return $user['type'] == 0;
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

        if ($user['id'] == $salesman['id']) {
            return false;
        }

        $group_id = $this->group->selectFirst([['salesman_id', '=', $user['id']]], 'id')['id'];

        if ($salesman['group'] == $group_id) {
            return true;
        }

        return false;
    }
}
