<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApprovalLevelRepository;
use App\Entities\ApprovalLevel;
use App\Validators\ApprovalLevelValidator;

/**
 * Class ApprovalLevelRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApprovalLevelRepositoryEloquent extends BaseRepository implements ApprovalLevelRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ApprovalLevel::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
