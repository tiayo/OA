<?php

namespace App\Repositories;

use App\User;

class UsersRepository
{
    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function create($data)
    {
        return $this->user->create($data);
    }
    
    public function get($page, $num, $keyword = null)
    {
        if (!empty($keyword)) {
            return $this->user
                ->where('type', 0)
                ->where(function ($query) use ($keyword) {
                    $query->where('name', $keyword)
                        ->orwhere('email', $keyword);
                })
                ->orderBy('id', 'desc')
                ->first();
        }

        return $this->user
            ->where('type', 0)
            ->skip(($page-1) * $num)
            ->take($num)
            ->orderBy('id', 'desc')
            ->get();
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

        return $this->user
            ->where('id', $id)
            ->update($post);
    }

    public function destroy($id)
    {
        return $this->user
            ->where('id', $id)
            ->delete();
    }
}