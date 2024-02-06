<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\FamilyCardRepository;
use App\Entities\FamilyCard;
use App\Validators\FamilyCardValidator;

/**
 * Class FamilyCardRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FamilyCardRepositoryEloquent extends BaseRepository implements FamilyCardRepository
{
    protected $fieldSearchable = [
        'no' => 'like',
        'components.user.name' => 'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FamilyCard::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
