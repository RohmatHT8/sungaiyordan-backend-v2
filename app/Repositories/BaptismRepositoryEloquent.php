<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BaptismRepository;
use App\Entities\Baptism;
use App\Validators\BaptismValidator;

/**
 * Class BaptismRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BaptismRepositoryEloquent extends BaseRepository implements BaptismRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Baptism::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
