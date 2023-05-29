<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CongregationalStatusComponentRepository;
use App\Entities\CongregationalStatusComponent;
use App\Validators\CongregationalStatusComponentValidator;

/**
 * Class CongregationalStatusComponentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CongregationalStatusComponentRepositoryEloquent extends BaseRepository implements CongregationalStatusComponentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CongregationalStatusComponent::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
