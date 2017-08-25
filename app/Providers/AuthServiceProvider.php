<?php

namespace App\Providers;

use App\Customer;
use App\Policies\CustomerPolicy;
use App\Policies\UserPolicy;
use App\Policies\VisitPolicy;
use App\User;
use App\Visit;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Customer::class => CustomerPolicy::class,
        Visit::class => VisitPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
