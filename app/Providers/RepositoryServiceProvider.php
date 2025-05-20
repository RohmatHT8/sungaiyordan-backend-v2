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
        $this->app->bind(\App\Repositories\FamilyCardWebRepository::class, \App\Repositories\FamilyCardWebRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\PermissionsRepository::class, \App\Repositories\PermissionsRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ShdrRepository::class, \App\Repositories\ShdrRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NumberSettingRepository::class, \App\Repositories\NumberSettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\NumberSettingComponentRepository::class, \App\Repositories\NumberSettingComponentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BaptismRepository::class, \App\Repositories\BaptismRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ChildSubmissionRepository::class, \App\Repositories\ChildSubmissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\MarriageCertificateRepository::class, \App\Repositories\MarriageCertificateRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ConfirmationOfMarriageRepository::class, \App\Repositories\ConfirmationOfMarriageRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FamilyCardRepository::class, \App\Repositories\FamilyCardRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\FamilyCardComponentRepository::class, \App\Repositories\FamilyCardComponentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CongregationalStatusRepository::class, \App\Repositories\CongregationalStatusRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\CongregationalStatusComponentRepository::class, \App\Repositories\CongregationalStatusComponentRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WidgetRepository::class, \App\Repositories\WidgetRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\UserWidgetRepository::class, \App\Repositories\UserWidgetRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WidgetPermissionRepository::class, \App\Repositories\WidgetPermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WidgetPermissionMappingRepository::class, \App\Repositories\WidgetPermissionMappingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\WidgetPermissionSettingRepository::class, \App\Repositories\WidgetPermissionSettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportRepository::class, \App\Repositories\ReportRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportPermissionRepository::class, \App\Repositories\ReportPermissionRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportPermissionMappingRepository::class, \App\Repositories\ReportPermissionMappingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportPermissionSettingRepository::class, \App\Repositories\ReportPermissionSettingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ReportPermissionMappingRepository::class, \App\Repositories\ReportPermissionMappingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\BuildingRepository::class, \App\Repositories\BuildingRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\RoomRepository::class, \App\Repositories\RoomRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemTypeRepository::class, \App\Repositories\ItemTypeRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemRepository::class, \App\Repositories\ItemRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemBranchRepository::class, \App\Repositories\ItemBranchRepositoryEloquent::class);
        $this->app->bind(\App\Repositories\ItemStatusRepository::class, \App\Repositories\ItemStatusRepositoryEloquent::class);
        //:end-bindings:
    }
}
