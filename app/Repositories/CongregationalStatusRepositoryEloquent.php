<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\CongregationalStatusRepository;
use App\Entities\CongregationalStatus;
use App\Validators\CongregationalStatusValidator;

/**
 * Class CongregationalStatusRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CongregationalStatusRepositoryEloquent extends BaseRepository implements CongregationalStatusRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CongregationalStatus::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
