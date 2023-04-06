<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PermissionMappingRepository;
use App\Entities\PermissionMapping;
use App\Validators\PermissionMappingValidator;

/**
 * Class PermissionMappingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionMappingRepositoryEloquent extends BaseRepository implements PermissionMappingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return PermissionMapping::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
