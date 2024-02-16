<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReportPermissionSettingRepository;
use App\Entities\ReportPermissionSetting;
use App\Validators\ReportPermissionSettingValidator;

/**
 * Class ReportPermissionSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReportPermissionSettingRepositoryEloquent extends BaseRepository implements ReportPermissionSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportPermissionSetting::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
