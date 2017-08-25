<?php

namespace App\Services\Admin;

use App\Customer;
use App\Repositories\CustomerRepository;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CustomerService
{
    protected $customer;

    public function __construct(CustomerRepository $customer)
    {
        $this->customer = $customer;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($page, $num, $keyword = null)
    {
        if (Auth::user()->can('admin', User::class)) {
            return $this->customer->adminGet($page, $num, $keyword);
        }

        return $this->customer->get($page, $num, $keyword);
    }

    /**
     * 统计数量
     *
     * @return mixed
     */
    public function countGet($keyword = null)
    {
        if (Auth::user()->can('admin', User::class)) {
            return $this->customer->adminCountGet($keyword);
        }

        return $this->customer->countGet($keyword);
    }

    /**
     * 查找指定id的用户
     *
     * @param $id
     * @return mixed
     */
    public function first($id)
    {
        //获取当前客户
        $customer = $this->customer->first($id);

        //验证操作权限
        if (Auth::user()->can('control', $customer)) {
            return $customer;
        }

        throw new \Exception('不是您的客户！');
    }

    /**
     * 获取超级管理员id
     * 默认数据表第一个管理员为超级管理员
     *
     * @return mixed
     */
    public function superId()
    {
        return $this->customer->superId()['id'];
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
        $add['email'] = $post['email'] ?? 0;
        $add['company'] = $post['company'];
        $add['remark'] = $post['remark'] ?? null;

        $customer = Customer::find($id);

        if (!empty($id) && !Auth::user()->can('control', $customer)) {
            throw new \Exception('不是您的客户！');
        }

        $this->customer->updateOrCreate($add, $id);

        if (!empty($id) && !empty($customer)) {
            return Log::info(
                "用户："
                .Auth::user()['name']."(".Auth::id().
                ")更新原客户:".
                json_encode($customer->toArray()).
                "现在客户:".json_encode(Customer::find($id)->toArray())
            );
        }
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
    public function unique($post)
    {
        //正向搜索
        $result = $this->customer->unique($post);

        //反向搜索
        if (empty($result)) {
            $result = $this->customer->reverseUnique($post);
        }

        if (!empty($result)) {
            throw new \Exception(
                '已经存在以下类似记录'."\r\n".
                '姓名：'.$result['name']."\r\n".
                '电话：'.$result['phone']."\r\n".
                '公司:'.$result['company']."\r\n".
                '请核对！'
            );
        }

        return true;
    }

    /**
     * 删除管理员
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);

        if (empty($customer)) {
            throw new \Exception('没有查询到客户！');
        }

        //验证操作权限
        if (!Auth::user()->can('control', $customer)) {
            throw new \Exception('不是您的客户！');
        }

        if ($this->customer->destroy($id))
        {
            return Log::info(
                "用户：" .
                Auth::user()['name']."(".Auth::id().
                ")删除了客户:".
                json_encode($customer->toArray())
            );
        }

        throw new \Exception('删除失败！');
    }
}