<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PermissionSettingRepository;
use App\Entities\PermissionSetting;
use App\Validators\PermissionSettingValidator;

/**
 * Class PermissionSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionSettingRepositoryEloquent extends BaseRepository implements PermissionSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PermissionSetting::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
