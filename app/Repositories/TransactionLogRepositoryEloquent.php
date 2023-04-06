<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\TransactionLogRepository;
use App\Entities\TransactionLog;
use App\Validators\TransactionLogValidator;

/**
 * Class TransactionLogRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TransactionLogRepositoryEloquent extends BaseRepository implements TransactionLogRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TransactionLog::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
