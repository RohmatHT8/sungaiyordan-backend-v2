<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Repositories\UserRepository::class, \App\Repositories\UserRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BranchRepository::class, \App\Repositories\BranchRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserBranchRepository::class, \App\Repositories\UserBranchRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\DepartmentRepository::class, \App\Repositories\DepartmentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoleRepository::class, \App\Repositories\RoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserRoleRepository::class, \App\Repositories\UserRoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TransactionRepository::class, \App\Repositories\TransactionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionRepository::class, \App\Repositories\PermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionSettingRepository::class, \App\Repositories\PermissionSettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionMappingRepository::class, \App\Repositories\PermissionMappingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApprovalRepository::class, \App\Repositories\ApprovalRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApprovalRoleRepository::class, \App\Repositories\ApprovalRoleRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApprovalLevelRepository::class, \App\Repositories\ApprovalLevelRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\TransactionLogRepository::class, \App\Repositories\TransactionLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ApprovalLogRepository::class, \App\Repositories\ApprovalLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\AccessLogRepository::class, \App\Repositories\AccessLogRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WebFamilyCardRepository::class, \App\Repositories\WebFamilyCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WebUserRepository::class, \App\Repositories\WebUserRepositoryEloquent::class);
        //:end-bindings:
    }
}
