<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WebFamilyCardRepository;
use App\Entities\WebFamilyCard;
use App\Validators\WebFamilyCardValidator;

/**
 * Class WebFamilyCardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WebFamilyCardRepositoryEloquent extends BaseRepository implements WebFamilyCardRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WebFamilyCard::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
