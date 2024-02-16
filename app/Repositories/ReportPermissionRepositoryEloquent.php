<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ReportPermissionRepository;
use App\Entities\ReportPermission;
use App\Validators\ReportPermissionValidator;

/**
 * Class ReportPermissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReportPermissionRepositoryEloquent extends BaseRepository implements ReportPermissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ReportPermission::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
