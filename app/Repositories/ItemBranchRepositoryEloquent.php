<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ItemBranchRepository;
use App\Entities\ItemBranch;
use App\Validators\ItemBranchValidator;

/**
 * Class ItemBranchRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemBranchRepositoryEloquent extends BaseRepository implements ItemBranchRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemBranch::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
