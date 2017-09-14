<?php

namespace App\Repositories;

use App\Group;
use App\Services\RedisServiceInterface;

class GroupRepository
{
    protected $group;
    protected $redis;

    public function __construct(Group $group, RedisServiceInterface $redis)
    {
        $this->group = $group;
        $this->redis = $redis;
    }

    public function create($data)
    {
        return $this->group->create($data);
    }

    public function update($id, $data)
    {
        return $this->group
            ->where('id', $id)
            ->update($data);
    }

    public function destroy($id)
    {
        return $this->group
            ->where('id', $id)
            ->delete();
    }

    public function find($id)
    {
        return $this->group->find($id);
    }

    public function search($page, $num, $keyword)
    {
        return $this->group
            ->where('name', 'like', "%$keyword%")
            ->skip(($page - 1) * $num)
            ->take($num)
            ->get();
    }

    public function get($page, $num)
    {
        return $this->group
            ->skip(($page - 1) * $num)
            ->take($num)
            ->get();
    }

    public function count()
    {
        return $this->group->count();
    }
}