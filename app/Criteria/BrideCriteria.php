<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BrideCriteria.
 *
 * @package namespace App\Criteria;
 */
class BrideCriteria implements CriteriaInterface
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
        return $model->where('gender', 'Perempuan')->whereNotIn('id', DB::table('marriage_certificates')->where('deleted_at',NULL)->pluck('bride'));
    }
}
