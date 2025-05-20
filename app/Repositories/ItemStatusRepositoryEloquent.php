<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ItemStatusRepository;
use App\Entities\ItemStatus;
use App\Validators\ItemStatusValidator;

/**
 * Class ItemStatusRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemStatusRepositoryEloquent extends BaseRepository implements ItemStatusRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemStatus::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
