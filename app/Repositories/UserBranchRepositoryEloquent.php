<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\UserBranchRepository;
use App\Entities\UserBranch;
use App\Validators\UserBranchValidator;

/**
 * Class UserBranchRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserBranchRepositoryEloquent extends BaseRepository implements UserBranchRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserBranch::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
