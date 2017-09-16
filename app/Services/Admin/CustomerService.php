<?php

namespace App\Services\Admin;

use App\Events\AddMessage;
use App\Repositories\CustomerRepository;
use Exception;

class CustomerService
{
    protected $customer;

    public function __construct(CustomerRepository $customer)
    {
        $this->customer = $customer;
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
        $salesman = $this->customer->first($id);

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
            return $this->customer->getSearch($num, $keyword);
        }

        return $this->customer->get($num);
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function getGroup($num = 10000, $group)
    {
        return $this->customer->getGroup($num, $group);
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
        $data['salesman_id'] = $post['salesman_id'];
        $data['name'] = $post['name'];
        $data['phone'] = $post['phone'];
        $data['wx'] = $post['wx'];
        $data['company'] = $post['company'];
        $data['remark'] = $post['remark'] ?? null;

        //验证操作更新操作的权限
        empty($id) ? $option = 2 : $origin = $this->validata($id)->toArray();

        //更新或插入
        $this->customer->updateOrCreate($data, $id);

        //执行写入消息事件
        return event(new AddMessage(
            'customer',
            $option ?? 1,
            $data,
            $origin ?? [],
            $id
        ));
    }

    /**
     * 判断是否有匹配的记录
     * 正向匹配为数据库查询
     * 反向匹配为php查询
     *
     * @param $post
     * @return bool
     * @throws \Exception
     */
    public function unique($post, $id = null)
    {
        //正向搜索
        $result = $this->customer->unique($post, $id);

        //反向搜索
        //if (empty($result)) {
        //    $result = $this->customer->reverseUnique($post, $id);
        //}

        if (!empty($result)) {
            throw new \Exception(
                '已经存在以下类似记录'."\r\n".
                '姓名：'.$result['name']."\r\n".
                '电话：'.$result['phone']."\r\n".
                '公司:'.$result['company']."\r\n".
                '微信:'.$result['wx']."\r\n".
                '请核对！'
            );
        }

        return true;
    }

    /**
     * 删除记录
     *
     * @param $id
     * @return bool
     */
    public function destroy($id)
    {
        //验证是否可以操作当前记录
        $this->validata($id);

        return $this->customer->destroy($id);
    }
}