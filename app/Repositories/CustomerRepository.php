<?php

namespace App\Repositories;

use App\Customer;
use App\Services\RedisServiceInterface;
use App\User;
use Illuminate\Support\Facades\Auth;

class CustomerRepository
{
    protected $customer;
    protected $customer_chunk;
    protected $salesman;
    protected $redis;

    public function __construct(Customer $customer, UsersRepository $salesman, RedisServiceInterface $redis)
    {
        $this->customer = $customer;
        $this->salesman = $salesman;
        $this->redis = $redis;
    }

    public function create($data)
    {
        $this->customer->create($data);

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_customer');
    }
    
    public function get($page, $num)
    {
        $customers = $this->getValue();

        return array_slice($customers, ($page-1)*$num, $num);
    }

    /**
     * 获取负责人及下级业务员的所有客户
     *
     * @return array
     */
    public function getValue()
    {
        //读redis缓存
        $redis = $this->redis->redisSingleGet('all_customer:'.Auth::id());

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
                    $data = $this->getCustomerBysalesmanId($salesman['id']);

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
            $this->redis->redisSingleAdd('all_customer:'.Auth::id(), serialize($redis), 1800);

            return $redis;
        }

        return unserialize($redis);
    }

    /**
     * 获取单个业务员客户
     *
     * @param $salesman_id
     * @return array
     */
    public function getCustomerBysalesmanId($salesman_id)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'customers.salesman_id', 'users.id')
            ->where('salesman_id', $salesman_id)
            ->get()
            ->toArray();
    }

    /**
     * 获取所有客户
     *
     * @return array
     */
    public function getAll()
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'customers.salesman_id', 'users.id')
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 获取客户数量
     *
     * @return int
     */
    public function countGet()
    {
        return count($this->getValue());
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
        $customers = $this->getValue();

        $result = [];

        foreach ($customers as $salesman) {
            if (
                strpos($salesman['name'], $keyword) !== false ||
                strpos($salesman['phone'], $keyword) !== false ||
                strpos($salesman['company'], $keyword) !== false ||
                strpos($salesman['wx'], $keyword) !== false
            ) {
                $result[] = $salesman;
            }
        }

        return ['data' => array_slice($result, ($page-1)*$num, $num), 'count' => count($result)];
    }
    
    public function first($id)
    {
        return $this->customer->find($id);
    }

    public function superId()
    {
        return $this->customer
            ->where('name', config('site.admin_name'))
            ->first();
    }

    public function updateOrCreate($post, $id)
    {
        if (empty($id) && $id !== 0) {
            $this->customer->create($post);
        } else {
            $this->customer
                ->where('id', $id)
                ->update($post);
        }

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_customer');
    }

    public function destroy($id)
    {
        $this->customer
            ->where('id', $id)
            ->delete();

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_customer');
    }

    public function unique($post, $id = 0)
    {
        return $this->customer
            ->where('id', '<>', $id)
            ->where(function ($query) use($post) {
                $query->where('name', 'like', '%'.$post['name'].'%')
                    ->orWhere('phone', 'like', '%'.$post['phone'].'%')
                    ->orWhere('company', 'like', '%'.$post['company'].'%')
                    ->orWhere('wx', $post['wx']);
            })
            ->first();
    }

    public function reverseUnique($post, $id = null)
    {
        $this->customer
            ->where('id', '<>', $id)
            ->chunk(50, function ($customers) use ($post) {
            foreach ($customers as $customer) {
                if (strpos($post['name'], $customer['name']) !== false) {
                    return $this->customer_chunk = $customer;
                }

                if (strpos($post['phone'], $customer['phone']) !== false) {
                    return $this->customer_chunk = $customer;
                }

                if (strpos($post['company'], $customer['company']) !== false) {
                    return $this->customer_chunk = $customer;
                }
            }
        });

        return $this->customer_chunk;
    }
}