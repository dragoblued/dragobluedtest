<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        $gate->define('USER_BASIC', function($user) {
            return $user->hasPermissions('USER_BASIC');
        });

        $gate->define('ADMIN_VIEW_MAIN', function($user) {
            return $user->hasPermissions('ADMIN_VIEW_MAIN');
        });

        $gate->define('ADMIN_VIEW_ALL', function($user) {
            return $user->hasPermissions('ADMIN_VIEW_ALL');
        });

        $gate->define('ADMIN_CREATE', function($user) {
            return $user->hasPermissions('ADMIN_CREATE');
        });

        $gate->define('ADMIN_EDIT', function($user) {
            return $user->hasPermissions('ADMIN_EDIT');
        });

        $gate->define('ADMIN_DELETE', function($user) {
            return $user->hasPermissions('ADMIN_DELETE');
        });

        //
    }
}
