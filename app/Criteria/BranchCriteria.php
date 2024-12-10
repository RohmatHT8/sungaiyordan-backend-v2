<?php

namespace App\Criteria;

use Illuminate\Support\Facades\Auth;
use Prettus\Repository\Contracts\CriteriaInterface;
use Prettus\Repository\Contracts\RepositoryInterface;

/**
 * Class BranchCriteria.
 *
 * @package namespace App\Criteria;
 */
class BranchCriteria implements CriteriaInterface
{
    protected $branchId;
    protected $specialClass;
    protected $foreignKey;
    protected $shdr;

    public function __construct($branchId=null,$specialClass=null,$foreignKey=null,$shdr=null){
        $this->branchId = $branchId;
        $this->specialClass = $specialClass;
        $this->foreignKey = $foreignKey;
        $this->shdr = $shdr;
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
        if(!empty($this->branchId) && $this->branchId == 'all'){
            return $model;
        }
        if(!empty($this->specialClass)){
            $branchIds = ($this->specialClass)::whereHas('branches', function($q){
                $q->whereIn('branches.id', Auth::user()->branches()->pluck('branches.id')->all());
                if(!empty($this->branchId)){
                    $q->where('branches.id',$this->branchId);
                }
            })->pluck('id')->all();
            $model = $model->whereIn($this->foreignKey,$branchIds);
        } else if(class_basename($repository) == 'BranchRepositoryEloquent'){
            $model = $model->whereIn('id',Auth::user()->branches()->pluck('branches.id')->all());
        } else if(!isset($model->branch_id) && !empty($model->branches)){
            $model = $model->whereHas('branches', function($q){
                $q->whereIn('branches.id', Auth::user()->branches()->pluck('branches.id')->all());
                if(!empty($this->branchId)){
                    $q->where('branches.id',$this->branchId);
                }
            });
        } else if($this->shdr) {
            $model = $model->whereIn($model->getModel()->getTable().'.place_of_shdr',Auth::user()->branches()->pluck('branches.id')->all());
            if(!empty($this->branchId)){
                $model = $model->where($model->getModel()->getTable().'.branch_id',$this->branchId);
            }
        }
        return $model;
    }
}
