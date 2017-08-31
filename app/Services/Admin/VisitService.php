<?php

namespace App\Services;

use App\Repositories\VisitRepository;
use App\Services\Admin\CustomerService;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
     * 获取需要的数据
     *
     * @return mixed
     */
    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->visit->getSearch($page, $num, $keyword);
        }

        return $this->visit->get($page, $num);
    }

    /**
     * 统计数量
     *
     * @return mixed
     */
    public function countGet()
    {
        return $this->visit->countGet();
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
        return $this->visit->first($id);
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
        $add['record'] = $post['record'] ?? null;

        if (empty($id)) {
            $add['salesman_id'] = $this->customer->first($post['customer_id'])['salesman_id'];
            $add['customer_id'] = $post['customer_id'];
        }

        if (!empty($id) && !Auth::user()->can('control', $visit = $this->visit->find($id))) {
            throw new \Exception('不是您的客户！');
        }

        $this->visit->updateOrCreate($add, $id);

        if (!empty($id) && !empty($visit)) {
            return Log::info(
                "用户："
                . Auth::user()['name'] . "(" . Auth::id() .
                ")更新原回访记录:" .
                json_encode($visit->toArray()) .
                "现在回访记录:" . json_encode($this->visit->find($id)->toArray())
            );
        }
    }

    /**
     * 删除管理员
     *
     * @param $id
     * @return bool|null
     */
    public function destroy($id)
    {
        $visit = $this->visit->find($id);

        if (empty($visit)) {
            throw new \Exception('没有查询到客户！');
        }

        //验证操作权限
        if (!Auth::user()->can('control', $visit)) {
            throw new \Exception('不是您的客户！');
        }

        if ($this->visit->destroy($id)) {
            return Log::info(
                "用户：" .
                Auth::user()['name'] . "(" . Auth::id() .
                ")删除了回访记录:" .
                json_encode($visit->toArray())
            );
        }

        throw new \Exception('删除失败！');
    }
}
