<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\NumberSettingComponentRepository;
use App\Entities\NumberSettingComponent;
use App\Validators\NumberSettingComponentValidator;

/**
 * Class NumberSettingComponentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NumberSettingComponentRepositoryEloquent extends BaseRepository implements NumberSettingComponentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NumberSettingComponent::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
