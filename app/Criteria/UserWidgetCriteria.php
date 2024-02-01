<?php

namespace App\Criteria;

use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserWidgetCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserWidgetCriteria implements CriteriaInterface
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
        if (Auth::user()->role_id != 1) {
            return $model->whereHas('permissions',function ($p){
                $p->whereHas('mappings',function ($q){
                    $q->whereIn('branch_id',Auth::user()->branches()->pluck('branches.id')->all())
                        ->whereIn('role_id',Auth::user()->getSubordinatesRoleId());
                });
            })->orWhere('default',true);
        }

        return $model;
    }
}
