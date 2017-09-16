<?php

namespace App\Services\Admin;

use App\Events\AddMessage;
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
        empty($id) ? $option = 2 : $origin = $this->validata($id)->toArray();

        //统计数据
        $data['name'] = $post['name'];
        $data['email'] = $post['email'];
        $data['type'] = $post['type'];

        //创建时指定分组
        $data['group'] = $post['group'] ?? Auth::user()['group'];

        //密码
        if (isset($post['password'])) {
            $data['password'] = bcrypt($post['password']);
        } else if(empty($id) && $id !== 0) {
            //默认密码
            $data['password'] = bcrypt('Abcd.123');
        }

        //执行操作
        $this->user->updateOrCreate($data, $id);

        //删除密码
        unset($data['password']);

        //执行写入消息事件
        return event(new AddMessage(
            'salesman',
            $option ?? 1,
            $data,
            $origin ?? []
        ));
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
        $orgin = $this->validata($id)->toArray();

        //执行删除
        $this->user->destroy($id);

        //执行写入消息事件
        return event(new AddMessage('salesman', 3, [], $orgin));
    }

    public function countGroup($group_id)
    {
        return $this->user->countGroup($group_id);
    }
}