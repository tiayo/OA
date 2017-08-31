<?php

namespace App\Repositories;

use App\Services\RedisServiceInterface;
use App\User;
use App\Visit;
use Illuminate\Support\Facades\Auth;

class VisitRepository
{
    protected $visit;
    protected $salesman;
    protected $redis;
    protected $visit_chunk;

    public function __construct(Visit $visit, UsersRepository $salesman, RedisServiceInterface $redis)
    {
        $this->visit = $visit;
        $this->salesman = $salesman;
        $this->redis = $redis;
    }

    public function create($data)
    {
        $this->visit->create($data);

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_visit');
    }

    public function get($page, $num)
    {
        $visits = $this->getValue();

        return array_slice($visits, ($page-1)*$num, $num);
    }

    /**
     * 获取搜索结果
     *
     * @param $page
     * @param $num
     * @param $keyword
     * @return array
     */
    public function getSearch($page, $num, $keyword)
    {
        $visits = $this->getValue();

        $result = [];

        foreach ($visits as $visit) {
            if ($visit['customer_id']== $keyword) {
                $result[] = $visit;
            }
        }

        return ['data' => array_slice($result, ($page-1)*$num, $num), 'count' => count($result)];
    }

    /**
     * 获取负责人及下级业务员的所有客户
     *
     * @return array
     */
    public function getValue()
    {
        //读redis缓存
        $redis = $this->redis->redisSingleGet('all_visit:'.Auth::id());

        if (empty($redis))
        {
            //初始化
            $redis = [];

            //超级管理员获取全部
            if (Auth::user()->can('admin', User::class))
            {
                $redis = $this->getAll();
            } else {
                //初始化
                $result = [];

                //加入下级
                $salesmans = $this->salesman->getChildren(Auth::id(), 'id', 'parent_id');

                //加入自己
                $salesmans[] = ['id' => Auth::id()];

                //获取所有客户
                foreach ($salesmans as $salesman) {

                    //获取单个业务员的客户
                    $data = $this->getVisitBysalesmanId($salesman['id']);

                    empty($data) ? : $result[] = $data;
                }

                //整理格式
                foreach ($result as $value) {
                    foreach ($value as $item) {
                        $redis[] =  $item;
                    }
                }
            }

            //写入redis
            $this->redis->redisSingleAdd('all_visit:'.Auth::id(), serialize($redis), 1800);

            return $redis;
        }

        return unserialize($redis);
    }

    /**
     * 获取所有客户
     *
     * @return array
     */
    public function getAll()
    {
        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 获取单个业务员客户
     *
     * @param $salesman_id
     * @return array
     */
    public function getVisitBysalesmanId($salesman_id)
    {
        return $this->visit
            ->where('visits.salesman_id', $salesman_id)
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    public function countGet()
    {
        return count($this->getValue());
    }

    public function AdminCountGet($keyword = null)
    {

        if (!empty($keyword)) {
            return $this->visit
                ->where('customer_id', $keyword)
                ->count();
        }

        return $this->visit
            ->count();
    }

    public function first($id)
    {
        return $this->visit
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'customers.name as customer_name')
            ->find($id);
    }

    public function superId()
    {
        return $this->visit
            ->where('name', config('site.admin_name'))
            ->first();
    }

    public function updateOrCreate($post, $id)
    {
        if (empty($id) && $id !== 0) {
            $this->visit->create($post);
        } else {
            $this->visit->where('id', $id)->update($post);
        }

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_visit');
    }

    public function destroy($id)
    {
        $this->visit
            ->where('id', $id)
            ->delete();

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_visit');
    }

    public function unique($post)
    {
        return $this->visit
            ->where('name', 'like', '%'.$post['name'].'%')
            ->orWhere('phone', 'like', '%'.$post['phone'].'%')
            ->orWhere('company', 'like', '%'.$post['company'].'%')
            ->first();
    }

    public function reverseUnique($post)
    {
        $this->visit->chunk(50, function ($customers) use ($post) {
            foreach ($customers as $customer) {
                if (strpos($post['name'], $customer['name']) !== false) {
                    return $this->visit_chunk = $customer;
                }

                if (strpos($post['phone'], $customer['phone']) !== false) {
                    return $this->visit_chunk = $customer;
                }

                if (strpos($post['company'], $customer['company']) !== false) {
                    return $this->visit_chunk = $customer;
                }
            }
        });

        return $this->visit_chunk;
    }

    public function find($id)
    {
        return $this->visit->find($id);
    }
}