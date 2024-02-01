<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WidgetPermissionMappingRepository;
use App\Entities\WidgetPermissionMapping;
use App\Validators\WidgetPermissionMappingValidator;

/**
 * Class WidgetPermissionMappingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WidgetPermissionMappingRepositoryEloquent extends BaseRepository implements WidgetPermissionMappingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WidgetPermissionMapping::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
