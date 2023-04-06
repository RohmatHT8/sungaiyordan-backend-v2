<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\AccessLogRepository;
use App\Entities\AccessLog;
use App\Validators\AccessLogValidator;

/**
 * Class AccessLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class AccessLogRepositoryEloquent extends BaseRepository implements AccessLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return AccessLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
