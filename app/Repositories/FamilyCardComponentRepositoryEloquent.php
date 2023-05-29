<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FamilyCardComponentRepository;
use App\Entities\FamilyCardComponent;
use App\Validators\FamilyCardComponentValidator;

/**
 * Class FamilyCardComponentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FamilyCardComponentRepositoryEloquent extends BaseRepository implements FamilyCardComponentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FamilyCardComponent::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
