<?php

namespace App\Criteria;

use App\Entities\User;
use App\Entities\WebUser;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class UserFamilyCardCriteria.
 *
 * @package namespace App\Criteria;
 */
class UserFamilyCardCriteria implements CriteriaInterface
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
        $idKK = WebUser::whereNotIn('nik',User::pluck('no_ktp'))->pluck('web_user_family_card_id'); 
        return $model->whereIn('id',$idKK);
    }
}
