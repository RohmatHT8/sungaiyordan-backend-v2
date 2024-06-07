<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Repositories\MarriageCertificateRepository;
use App\Entities\MarriageCertificate;
use App\Validators\MarriageCertificateValidator;

/**
 * Class MarriageCertificateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class MarriageCertificateRepositoryEloquent extends BaseRepository implements MarriageCertificateRepository
{
    protected $fieldSearchable = [
        'no' => 'like',
        'grooms.name' => 'like',
        'brides.name' => 'like'
    ];
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return MarriageCertificate::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
