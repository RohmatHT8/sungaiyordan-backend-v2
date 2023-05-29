<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\NumberSettingRepository;
use App\Entities\NumberSetting;
use App\Validators\NumberSettingValidator;

/**
 * Class NumberSettingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class NumberSettingRepositoryEloquent extends BaseRepository implements NumberSettingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return NumberSetting::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
