<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReportPermissionMappingRepository;
use App\Entities\ReportPermissionMapping;
use App\Validators\ReportPermissionMappingValidator;

/**
 * Class ReportPermissionMappingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReportPermissionMappingRepositoryEloquent extends BaseRepository implements ReportPermissionMappingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportPermissionMapping::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
