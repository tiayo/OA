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
        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->where('visits.salesman_id', Auth::id())
            ->orderBy('id', 'desc')
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

        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->join('groups', 'users.group', 'groups.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->where('users.group', $group)
            ->orderBy('id', 'desc')
            ->paginate($num);
    }

    /**
     * 获取所有显示记录（超级管理员级别）
     *
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function adminGet($num)
    {
        return $this->visit
            ->leftjoin('users', 'visits.salesman_id', 'users.id')
            ->leftjoin('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->orderBy('id', 'desc')
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
        $option = explode('_', $keyword);

        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->where('visits.salesman_id', Auth::id())
            ->where('visits.'.$option[0].'_id', 'like', "%$option[1]%")
            ->orderBy('id', 'desc')
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
        $option = explode('_', $keyword);

        return $this->visit
            ->join('users', 'visits.salesman_id', 'users.id')
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->join('groups', 'users.group', 'groups.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->where('users.group', Auth::user()['group'])
            ->where('visits.'.$option[0].'_id', 'like', "%$option[1]%")
            ->orderBy('id', 'desc')
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
        $option = explode('_', $keyword);

        //靠分组搜索，模拟‘manageGet’方法，将分组传入获取数据
        if ($option[0] == 'group') {
            return $this->manageGet($num, $option[1]);
        }

        return $this->visit
            ->leftjoin('users', 'visits.salesman_id', 'users.id')
            ->leftjoin('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'users.name as salesman_name', 'customers.name as customer_name')
            ->where('visits.'.$option[0].'_id', 'like', "%$option[1]%")
            ->orderBy('id', 'desc')
            ->paginate($num);
    }

    public function first($id)
    {
        return $this->visit
            ->join('customers', 'visits.customer_id', 'customers.id')
            ->select('visits.*', 'customers.name as customer_name')
            ->find($id);
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