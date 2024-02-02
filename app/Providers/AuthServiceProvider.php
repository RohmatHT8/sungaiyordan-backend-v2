<?php

namespace App\Providers;

use App\Util\Helper;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

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
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addHours(13));
        Passport::refreshTokensExpireIn(now()->addHours(13));
        
        Gate::before(function ($user, $ability) {
            if ($user->role_id == 1 || $user->hasAuthority(Helper::getAliasAbility($ability))) {
                return true;
            }
        });

    }
}
