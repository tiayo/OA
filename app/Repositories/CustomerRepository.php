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

    /**
     * 获取所有显示记录（调度）
     *
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($num)
    {
        return can('admin') ? $this->adminGet($num) :
            (can('user') ? $this->userGet($num) : $this->manageGet($num));
    }

    /**
     * 获取所有显示记录（用户级别）
     *
     * @param $page
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function userGet($num)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'users.id', 'customers.salesman_id')
            ->where('customers.salesman_id', Auth::id())
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取所有显示记录（组长级别）
     *
     * @param $page
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function manageGet($num, $group = null)
    {
        $group = $group ?? Auth::user()['group'];

        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'users.id', 'customers.salesman_id')
            ->where('users.group', $group)
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取所有显示记录（超级管理员级别）
     *
     * @param $page
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function adminGet($num)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->leftjoin('users', 'users.id', 'customers.salesman_id')
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取显示的搜索结果（调度）
     *
     * @param $num
     * @param $keyword
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getSearch($num, $keyword)
    {
        return can('admin') ? $this->getAdminSearch($num, $keyword) :
            (can('user') ? $this->getUserSearch($num, $keyword) : $this->getManageSearch($num, $keyword));
    }

    /**
     * 获取显示的搜索结果（用户级）
     *
     * @param $num
     * @param $keyword
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getUserSearch($num, $keyword)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'users.id', 'customers.salesman_id')
            ->where('customers.salesman_id', Auth::id())
            ->where(function ($query) use ($keyword) {
                $query->where('customers.name', 'like', "%$keyword%")
                    ->orwhere('customers.wx', 'like', "%$keyword%")
                    ->orwhere('customers.phone', 'like', "%$keyword%")
                    ->orwhere('customers.company', 'like', "%$keyword%");
            })
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取显示的搜索结果（组长级）
     *
     * @param $num
     * @param $keyword
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getManageSearch($num, $keyword)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->join('users', 'users.id', 'customers.salesman_id')
            ->where('users.group', Auth::user()['group'])
            ->where(function ($query) use ($keyword) {
                $query->where('customers.name', 'like', "%$keyword%")
                    ->orwhere('customers.wx', 'like', "%$keyword%")
                    ->orwhere('customers.phone', 'like', "%$keyword%")
                    ->orwhere('customers.company', 'like', "%$keyword%");
            })
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取显示的搜索结果（超级管理员级）
     *
     * @param $num
     * @param $keyword
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAdminSearch($num, $keyword)
    {
        return $this->customer
            ->select('customers.*', 'users.name as salesman_name')
            ->leftjoin('users', 'users.id', 'customers.salesman_id')
            ->where(function ($query) use ($keyword) {
                $query->where('customers.name', 'like', "%$keyword%")
                    ->orwhere('customers.wx', 'like', "%$keyword%")
                    ->orwhere('customers.phone', 'like', "%$keyword%")
                    ->orwhere('customers.company', 'like', "%$keyword%");
            })
            ->orderBy('customers.id', 'desc')
            ->paginate($num);
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
                $query->where('name', $post['name'])
                    ->orWhere(function ($query) use ($post) {
                        $query->where('phone', $post['phone'])
                            ->where('phone', '<>', '无');
                    })
                    ->orWhere(function ($query) use ($post) {
                        $query->where('company', $post['company'])
                            ->where('company', '<>', '无');
                    })
                    ->orWhere(function ($query) use ($post) {
                        $query->where('wx', $post['wx'])
                            ->where('wx', '<>', '无');
                    });
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

    /**
     * 按分组显示记录（超级管理级别）
     *
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getGroup($num, $group)
    {
        //靠分组查看，模拟‘manageGet’方法，将分组传入获取数据
        return $this->manageGet($num, $group);
    }
}