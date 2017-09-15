<?php

namespace App\Services\Admin;

use App\Repositories\UsersRepository;
use Illuminate\Support\Facades\Auth;
use Exception;

class SalesmanService
{
    protected $user;

    public function __construct(UsersRepository $user)
    {
        $this->user = $user;
    }

    /**
     * 通过id验证记录是否存在以及是否有操作权限
     * 通过：返回该记录
     * 否则：抛错
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null|static|static[]
     */
    public function validata($id)
    {
        $salesman = $this->user->first($id);

        throw_if(empty($salesman), Exception::class, '未找到该用户！', 404);

        throw_if(!can('control', $salesman), Exception::class, '没有权限！', 403);

        return $salesman;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($num = 10000, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->user->getSearch($num, $keyword);
        }

        return $this->user->get($num);
    }

    /**
     * 查找指定id的用户
     *
     * @param $id
     * @return mixed
     */
    public function first($id)
    {
        return $this->validata($id);
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
        //验证是否可以操作当前记录
        empty($id) ? : $this->validata($id);

        //统计数据
        $add['name'] = $post['name'];
        $add['email'] = $post['email'];
        $add['type'] = $post['type'];

        //创建时指定分组
        $add['group'] = $post['group'] ?? Auth::user()['group'];

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
        //验证是否可以操作当前记录
        $this->validata($id);

        return $this->user->destroy($id);
    }

    public function countGroup($group_id)
    {
        return $this->user->countGroup($group_id);
    }
}