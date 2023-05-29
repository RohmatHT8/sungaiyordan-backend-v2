<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ChildSubmissionRepository;
use App\Entities\ChildSubmission;
use App\Validators\ChildSubmissionValidator;

/**
 * Class ChildSubmissionRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ChildSubmissionRepositoryEloquent extends BaseRepository implements ChildSubmissionRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ChildSubmission::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
