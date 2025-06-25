<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BudgetRepository;
use App\Entities\Budget;

/**
 * Class BudgetRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BudgetRepositoryEloquent extends BaseRepository implements BudgetRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Budget::class;
    }

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
