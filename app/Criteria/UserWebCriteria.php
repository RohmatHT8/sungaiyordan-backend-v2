<?php

namespace App\Criteria;

use App\Entities\User;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserWebCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserWebCriteria implements CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param string              $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository)
    {
        $nik = User::pluck('no_ktp');
        return $model->whereNotIn('nik',$nik);
    }
}
