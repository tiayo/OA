<?php

namespace App\Repositories;

use App\Group;
use App\User;
use Illuminate\Support\Facades\Auth;

class UsersRepository
{
    protected $user;
    protected $group;

    public function __construct(User $user, Group $group)
    {
        $this->user = $user;
        $this->group = $group;
    }

    public function create($data)
    {
        return $this->user->create($data);
    }

    /**
     * 获取所有显示记录（调度）
     *
     * @param $num
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function get($num)
    {
        return can('admin') ? $this->adminGet($num) : $this->userGet($num);
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
        return $this->user
            ->where('users.group', Auth::user()['group'])
            ->where([
                ['users.type', '<>', 1],
                ['users.type', '<>', 2],
            ])
            ->join('groups', 'groups.id', 'users.group')
            ->select('users.*', 'groups.name as group_name')
            ->orderBy('id', 'desc')
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
        return $this->user
            ->where('type', '<>', 1)
            ->leftjoin('groups', 'groups.id', 'users.group')
            ->select('users.*', 'groups.name as group_name')
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
        return can('admin') ? $this->getAdminSearch($num, $keyword) : $this->getUserSearch($num, $keyword);
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
        return $this->user
            ->where('users.group', Auth::user()['group'])
            ->where([
                ['users.type', '<>', 1],
                ['users.type', '<>', 2],
            ])
            ->where(function ($query) use ($keyword) {
                $query->where('users.name', 'like', "%$keyword%")
                    ->orwhere('users.email', 'like', "%$keyword%");
            })
            ->join('groups', 'groups.id', 'users.group')
            ->select('users.*', 'groups.name as group_name')
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
        return $this->user
            ->where('type', '<>', 1)
            ->where(function ($query) use ($keyword) {
                $query->where('users.name', 'like', "%$keyword%")
                    ->orwhere('users.email', 'like', "%$keyword%");
            })
            ->leftjoin('groups', 'groups.id', 'users.group')
            ->select('users.*', 'groups.name as group_name')
            ->orderBy('id', 'desc')
            ->paginate($num);
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

        return$this->user->where('id', $id)->update($post);
    }

    public function destroy($id)
    {
        return $this->user
            ->where('id', $id)
            ->delete();
    }

    public function countGroup($group_id)
    {
        return $this->user
            ->where('group', $group_id)
            ->count();
    }

    public function selectFirst($where, ...$select)
    {
        return $this->user
            ->select($select)
            ->where($where)
            ->first();
    }
}