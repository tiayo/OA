<?php

namespace App\Services\Admin;

use App\Repositories\UsersRepository;
use App\User;
use Illuminate\Support\Facades\Auth;

class SalesmanService
{
    protected $user;

    public function __construct(UsersRepository $user)
    {
        $this->user = $user;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->user->getSearch($page, $num, $keyword);
        }

        return $this->user->get($page, $num);
    }

    /**
     * 统计数量
     *
     * @return mixed
     */
    public function countGet()
    {
        return $this->user->countGet();
    }

    /**
     * 查找指定id的用户
     *
     * @param $id
     * @return mixed
     */
    public function first($id)
    {
        return $this->user->first($id);
    }

    /**
     * 获取超级管理员id
     * 默认数据表第一个管理员为超级管理员
     *
     * @return mixed
     */
    public function superId()
    {
        return $this->user->superId()['id'];
    }

    /**
     * 更新或编辑
     *
     * @param $post
     * @param null $id
     * @return mixed
     */
    public function updateOrCreate($post, $id = null)
    {
        //验证是否可以操作当前业务员
        if (!empty($id)) {
            if (!Auth::user()->can('control', User::find($id))) {
                throw new \Exception('没有权限操作该业务员！');
            }
        }

        //统计数据
        $add['name'] = $post['name'];
        $add['email'] = $post['email'];
        $add['type'] = $post['type'];

        //创建时指定父级
        if (empty($id)) {
            $add['parent_id'] = Auth::id();
        }

        //密码
        if (isset($post['password'])) {
            $add['password'] = bcrypt($post['password']);
        } else if(empty($id) && $id !== 0) {
            //默认密码
            $add['password'] = bcrypt('Abcd.123');
        }

        return $this->user->updateOrCreate($add, $id);
    }

    /**
     * 删除管理员
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        return $this->user->destroy($id);
    }
}