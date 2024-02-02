<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ApprovalLogRepository;
use App\Entities\ApprovalLog;

/**
 * Class ApprovalLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ApprovalLogRepositoryEloquent extends BaseRepository implements ApprovalLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ApprovalLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
