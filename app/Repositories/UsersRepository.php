<?php

namespace App\Repositories;

use App\Services\RedisServiceInterface;
use App\User;
use Illuminate\Support\Facades\Auth;

class UsersRepository
{
    protected $user;
    protected $redis;

    public function __construct(User $user, RedisServiceInterface $redis)
    {
        $this->user = $user;
        $this->redis = $redis;
    }

    public function create($data)
    {
        $this->user->create($data);

        return $this->redis->redisMultiDelete('all_salesman');
    }

    /**
     * 获取记录调度方法
     *
     * @param $page
     * @param $num
     * @return array
     */
    public function get($page, $num)
    {
        $all_salesman = $this->getValue();

        return array_slice($all_salesman, ($page-1)*$num, $num);
    }

    /**
     * 从redis获取缓存或写入缓存
     *
     * @return array|mixed
     */
    public function getValue()
    {
        //从redis获取数据
        $all_salesman = $this->redis->redisSingleGet('all_salesman:'.Auth::id());

        //没有缓存
        if (empty($all_salesman)) {
            if (Auth::user()->can('admin', User::class)) {
                $all_salesman = $this->getAll();
            } else {
                $all_salesman = $this->getChildren(Auth::id());
            }

            $this->redis->redisSingleAdd('all_salesman:'.Auth::id(), serialize($all_salesman), 1800);
        } else {

            $all_salesman = unserialize($all_salesman);
        }

        return $all_salesman;
    }

    /**
     * 获取下级
     *
     * @param $parent
     * @param array ...$select
     * @return mixed
     */
    public function getChildren($parent, ...$select)
    {
        $select = !empty($select) ? $select : '*';

        $all_children = $this->getAll($select);

        if (isset($this->tree($all_children)[$parent])) {
            return $this->tree($all_children)[$parent];
        } else {
            return [];
        }
    }

    /**
     * 获取所有非超级管理员用户
     *
     * @param string $select
     * @return array
     */
    public function getAll($select = '*')
    {
        return $this->user
            ->select($select)
            ->where('type', '<>', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();
    }

    /**
     * 创建目录树.
     *
     * @param $items
     *
     * @return mixed
     */
    public function tree($items)
    {
        $childs = [];

        foreach ($items as &$item) {
            $childs[$item['parent_id']][] = &$item;
        }

        unset($item);

        foreach ($items as &$item) {
            if (isset($childs[$item['id']])) {
                $item['childs'] = $childs[$item['id']];
            }
        }

        return $childs;
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
        $all_salesman = $this->getValue();

        $result = [];

        foreach ($all_salesman as $salesman) {
            if (strpos($salesman['name'], $keyword) !== false || strpos($salesman['email'], $keyword) !== false) {
                $result[] = $salesman;
            }
        }

        return ['data' => array_slice($result, ($page-1)*$num, $num), 'count' => count($result)];
    }

    public function countGet()
    {
        return count($this->getValue());
    }
    
    public function first($id)
    {
        return $this->user->find($id);
    }

    public function superId()
    {
        return $this->user
            ->where('name', config('site.admin_name'))
            ->first();
    }

    public function updateOrCreate($post, $id)
    {
        if (empty($id) && $id !== 0) {
            $this->user->create($post);
        } else {
            $this->user->where('id', $id)->update($post);
        }

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_salesman');
    }

    public function destroy($id)
    {
        $this->user
            ->where('id', $id)
            ->delete();

        //删除redis缓存
        return $this->redis->redisMultiDelete('all_salesman');
    }
}