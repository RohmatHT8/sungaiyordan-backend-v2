<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FamilyCard.
 *
 * @package namespace App\Entities;
 */
class FamilyCard extends Model implements Transformable
{
    use TransformableTrait,SoftDeletes,TransactionLogModelTrait;

    protected $fillable = ['branch_id','no','address'];

    protected $append = ['can_delete'];

    public function branch(){
       return $this->belongsTo('App\Entities\Branch','branch_id');
    }

    public function components(){
        return $this->hasMany('App\Entities\FamilyCardComponent');
    }

    public function getCanDeleteAttribute() {
        return $this->defaultCanDeleteAttribute();
    }

}
