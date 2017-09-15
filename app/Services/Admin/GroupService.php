<?php

namespace App\Services\Admin;

use App\Events\AddMessage;
use App\Repositories\GroupRepository;
use App\Repositories\UsersRepository;

class GroupService
{
    protected $group;
    protected $salesman;
    protected $user;

    public function __construct(GroupRepository $group, SalesmanService $salesman, UsersRepository $user)
    {
        $this->group = $group;
        $this->salesman = $salesman;
        $this->user = $user;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($num = 10000, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->group->search($num, $keyword);
        }

        return $this->group->get($num);
    }

    /**
     * 查找指定id的用户
     *
     * @param $id
     * @return mixed
     */
    public function first($id)
    {
        return $this->group->find($id);
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
        $data['name'] = $post['name'];
        $data['salesman_id'] = $new_user_id = $post['salesman_id'];

        //获取原用户
        empty($id) ? $option = 2 : $origin = $this->first($id)->toArray();

        $group_id  = empty($id) ? $this->group->create($data)->id : $this->group->update($id, $data);

        //更新或插入用户
        $this->updateGroup($group_id, $new_user_id, $origin['salesman_id'] ?? 0);

        //执行写入消息事件
        return event(new AddMessage(
            'group',
            $option ?? 1,
            $data,
            $origin ?? [],
            $id
        ));
    }

    /**
     * 更新分组关系
     *
     * @param $group_id
     * @param $new_user_id
     * @param $origin_user_id
     */
    public function updateGroup($group_id, $new_user_id, $origin_user_id = 0)
    {
        //未产生更新的，跳过
        if ($new_user_id == $origin_user_id) {
            return true;
        }

        //更新新用户
        $this->user->updateOrCreate(['group' => $group_id], $new_user_id);

        //更新原用户
        $this->user->updateOrCreate(['group' => 0], $origin_user_id);
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        //如果分组下有用户，则不可以删除
        if ($this->salesman->countGroup($id) > 0) {
            return false;
        }

        return $this->group->destroy($id);
    }

    /**
     * 返回所有业务员
     * 限制上限10000条，可以修改
     *
     * @return mixed
     */
    public function getAllSalesman()
    {
        return $this->salesman->get(10000);
    }
}