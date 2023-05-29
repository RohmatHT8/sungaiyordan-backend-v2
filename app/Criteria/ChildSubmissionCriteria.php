<?php

namespace App\Criteria;

use Illuminate\Support\Facades\DB;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class ChildSubmissionCriteria.
 *
 * @package namespace App\Criteria;
 */
class ChildSubmissionCriteria implements CriteriaInterface
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
        return $model->whereNotIn('id', DB::table('child_submissions')->where('deleted_at',NULL)->pluck('user_id'));
    }
}
