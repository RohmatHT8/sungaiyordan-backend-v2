<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ConfirmationOfMarriageRepository;
use App\Entities\ConfirmationOfMarriage;
use App\Validators\ConfirmationOfMarriageValidator;

/**
 * Class ConfirmationOfMarriageRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ConfirmationOfMarriageRepositoryEloquent extends BaseRepository implements ConfirmationOfMarriageRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ConfirmationOfMarriage::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
