<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function admin($user)
    {
        return $user['name'] === config('site.admin_name');
    }

    public function view($user)
    {
        return $user['name'] === config('site.admin_name');
    }
}
