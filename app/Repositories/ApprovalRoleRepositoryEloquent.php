<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApprovalRoleRepository;
use App\Entities\ApprovalRole;
use App\Validators\ApprovalRoleValidator;

/**
 * Class ApprovalRoleRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApprovalRoleRepositoryEloquent extends BaseRepository implements ApprovalRoleRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ApprovalRole::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
