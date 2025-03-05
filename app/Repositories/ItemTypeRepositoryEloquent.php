<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ItemTypeRepository;
use App\Entities\ItemType;
use App\Validators\ItemTypeValidator;

/**
 * Class ItemTypeRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ItemTypeRepositoryEloquent extends BaseRepository implements ItemTypeRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ItemType::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
