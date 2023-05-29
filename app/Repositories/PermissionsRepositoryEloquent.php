<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\PermissionsRepository;
use App\Entities\Permissions;
use App\Validators\PermissionsValidator;

/**
 * Class PermissionsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class PermissionsRepositoryEloquent extends BaseRepository implements PermissionsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Permissions::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
