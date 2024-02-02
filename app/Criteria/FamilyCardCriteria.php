<?php

namespace App\Criteria;

use App\Entities\FamilyCardComponent;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class FamilyCardCriteria.
 *
 * @package namespace App\Criteria;
 */
class FamilyCardCriteria implements CriteriaInterface
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
        $ids = FamilyCardComponent::where('status','Kepala Keluarga')
               ->orWhere('status','Istri')
               ->orWhere(function($q) {
                   $q->where('status', 'Anak')
                     ->whereNull('valid_until');
               })
               ->pluck('user_id');
        return $model->whereNotIn('id',$ids);
    }
}
