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
        return $this->user->create($data);
    }

    public function get($page, $num)
    {
        if (Auth::user()->can('admin', User::class)) {
            return $this->user
                ->where('type', '<>', 1)
                ->orderBy('id', 'desc')
                ->get()
                ->toArray();
        }

        //从redis获取数据
        $all_salesman = $this->redis->redisSingleGet('all_salesman:'.Auth::id());

        //没有缓存
        if (empty($all_salesman)) {

            $all_salesman = $this->getChildren(Auth::id());

            $this->redis->redisSingleAdd('all_salesman:'.Auth::id(), serialize($all_salesman), 1800);
        } else {

            $all_salesman = unserialize($all_salesman);
        }

        return array_slice($all_salesman, ($page-1)*$num, $num);

    }

    public function getChildren($parent)
    {
        $all_children = $this->user
            ->where('type', '<>', 1)
            ->orderBy('id', 'desc')
            ->get()
            ->toArray();

        return $this->tree($all_children)[$parent];
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

    public function getSearch($keyword)
    {
        return $this->user
            ->where('type', 0)
            ->where('')
            ->where(function ($query) use ($keyword) {
                $query->where('name', 'like', "%$keyword%")
                    ->orwhere('email', 'like', "%$keyword%");
            })
            ->orderBy('id', 'desc')
            ->first();
    }

    public function countGet()
    {
        return $this->user
            ->where('type', 0)
            ->count();
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
            return $this->user->create($post);
        }

        $this->user->where('id', $id)->update($post);

        //删除redis缓存
        return $this->redis->redisSingleDelete('all_salesman:'.Auth::id());
    }

    public function destroy($id)
    {
        return $this->user
            ->where('id', $id)
            ->delete();
    }
}