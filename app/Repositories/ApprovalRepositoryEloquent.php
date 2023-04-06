<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApprovalRepository;
use App\Entities\Approval;
use App\Validators\ApprovalValidator;

/**
 * Class ApprovalRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApprovalRepositoryEloquent extends BaseRepository implements ApprovalRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Approval::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
