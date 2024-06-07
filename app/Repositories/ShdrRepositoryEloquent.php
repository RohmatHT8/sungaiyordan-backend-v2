<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\ShdrRepository;
use App\Entities\Shdr;
use App\Validators\ShdrValidator;

/**
 * Class ShdrRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShdrRepositoryEloquent extends BaseRepository implements ShdrRepository
{
    protected $fieldSearchable = [
        'no' => 'like',
        'user.name' => 'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Shdr::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
