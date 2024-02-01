<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Baptism.
 *
 * @package namespace App\Entities;
 */
class Baptism extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['no', 'date', 'place_of_baptism_inside', 'place_of_baptism_outside', 'user_id', 'who_baptism'];

    protected $append = ['can_delete'];

    public function user(){
        return $this->belongsTo('App\Entities\User','user_id');
    }
    
    public function branch(){
        return $this->belongsTo('App\Entities\Branch','place_of_baptism_inside');
    }

    public function getCanDeleteAttribute() {
        return true;
    }

    public function getCanPrintAttribute() {
        return $this->defaultCanPrintAttribute();
    }

    public function getCanUpdateAttribute()
    {
    return $this->defaultCanUpdateAttribute();
    }

}
