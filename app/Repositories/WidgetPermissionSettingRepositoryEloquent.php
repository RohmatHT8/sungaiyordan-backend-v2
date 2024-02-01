<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WidgetPermissionSettingRepository;
use App\Entities\WidgetPermissionSetting;
use App\Validators\WidgetPermissionSettingValidator;

/**
 * Class WidgetPermissionSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WidgetPermissionSettingRepositoryEloquent extends BaseRepository implements WidgetPermissionSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WidgetPermissionSetting::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
