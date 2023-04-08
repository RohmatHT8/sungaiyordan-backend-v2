<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BranchRepository;
use App\Entities\Branch;
use App\Validators\BranchValidator;
use Prettus\Repository\Traits\CacheableRepository;

/**
 * Class BranchRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BranchRepositoryEloquent extends BaseRepository implements BranchRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    use CacheableRepository;
    protected $fieldSearchable = [
        'code' => 'like',
        'name' => 'like',
        'shepherd.name' => 'like',
    ];
    public function model()
    {
        return Branch::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
