<?php

namespace App\Criteria;

use App\Entities\UserBranch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BranchCriteria.
 *
 * @package namespace App\Criteria;
 */
class FamilyCardPerBranchCriteria implements CriteriaInterface
{
    protected $branchId;
    protected $specialClass;
    protected $foreignKey;

    public function __construct(){
        // 
    }

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
        $branchIds = UserBranch::where('user_id', Auth::user()->id)->pluck('branch_id')->all();
        return $model->whereIn('branch_id',$branchIds);
    }
}
