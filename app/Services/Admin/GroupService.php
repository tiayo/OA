<?php

namespace App\Services\Admin;

use App\Customer;
use App\Repositories\GroupRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GroupService
{
    protected $group;

    public function __construct(GroupRepository $group)
    {
        $this->group = $group;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->group->search($page, $num, $keyword);
        }

        return $this->group->get($page, $num);
    }

    /**
     * 统计数量
     *
     * @return mixed
     */
    public function countGet()
    {
        return $this->group->count();
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
        $add['salesman_id'] = $post['salesman_id'];
        $add['name'] = $post['name'];
        $add['phone'] = $post['phone'];
        $add['wx'] = $post['wx'];
        $add['company'] = $post['company'];
        $add['remark'] = $post['remark'] ?? null;

        if (!empty($id)) {
            return $this->group->update($id, $add);
        }

        return $this->group->create($add);
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        return $this->group->destroy($id);
    }
}