<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class GroomcomCriteria.
 *
 * @package namespace App\Criteria;
 */
class GroomcomCriteria implements CriteriaInterface
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
        return $model->where('gender', 'Laki-Laki')->whereNotIn('id', DB::table('confirmation_of_marriages')->where('deleted_at',NULL)->pluck('groom'));
    }
}
