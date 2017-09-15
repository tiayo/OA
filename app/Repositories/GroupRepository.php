<?php

namespace App\Repositories;

use App\Group;
use App\Services\RedisServiceInterface;
use Illuminate\Auth\Authenticatable;

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

    public function search($num, $keyword)
    {
        return $this->group
            ->where('groups.name', 'like', "%$keyword%")
            ->join('users', 'groups.salesman_id', 'users.id')
            ->select('groups.*', 'users.name as salesman_name')
            ->paginate($num);
    }

    public function get($num)
    {
        return $this->group
            ->join('users', 'groups.salesman_id', 'users.id')
            ->select('groups.*', 'users.name as salesman_name')
            ->paginate($num);
    }

    public function selectFirst($where, ...$select)
    {
        return $this->group
            ->select($select)
            ->where($where)
            ->first();
    }
}