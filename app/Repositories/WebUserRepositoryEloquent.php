<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\WebUserRepository;
use App\Entities\WebUser;
use App\Validators\WebUserValidator;

/**
 * Class WebUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class WebUserRepositoryEloquent extends BaseRepository implements WebUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return WebUser::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
