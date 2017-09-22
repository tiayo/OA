<?php

namespace App\Services\Admin;

use App\Events\AddMessage;
use App\Repositories\VisitRepository;
use App\Services\Admin\CustomerService;
use Exception;
use Illuminate\Support\Facades\Auth;

class VisitService
{
    protected $visit;
    protected $customer;

    public function __construct(VisitRepository $visit, CustomerService $customer)
    {
        $this->visit = $visit;
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
        $visit = $this->visit->first($id);

        throw_if(empty($visit), Exception::class, '未找到该记录！', 404);

        throw_if(!can('control', $visit), Exception::class, '没有权限！', 403);

        return $visit;
    }

    /**
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->visit->getSearch($num, $keyword);
        }

        return $this->visit->get($num);
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
        $data['record'] = $post['record'] ?? null;

        if (empty($id)) {
            //标记操作(插入)
            $option = 2;

            //构造数据
            $data['salesman_id'] = Auth::id();
            $data['customer_id'] = $post['customer_id'];

        } else {
            //标记操作(更新)
            $option = 1;

            //验证是否可以操作当前记录
            $origin = $this->validata($id)->toArray();
        }

        //执行操作
        $this->visit->updateOrCreate($data, $id);

        //执行写入消息事件
        return event(new AddMessage(
            'visit',
            $option,
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
        $origin = $this->validata($id)->toArray();

        //执行删除
        $this->visit->destroy($id);

        //执行写入消息事件
        return event(new AddMessage('visit', 3, [], $origin));
    }
}
