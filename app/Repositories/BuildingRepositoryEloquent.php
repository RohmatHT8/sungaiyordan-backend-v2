<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\BuildingRepository;
use App\Entities\Building;
use App\Validators\BuildingValidator;

/**
 * Class BuildingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BuildingRepositoryEloquent extends BaseRepository implements BuildingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Building::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
