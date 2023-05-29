<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class MarriageCertificate.
 *
 * @package namespace App\Entities;
 */
class MarriageCertificate extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, TransactionLogModelTrait;

    protected $fillable = ['no','date','branch_id','branch_non_local','groom','bride','who_blessed', 'location'];

    
    public function grooms(){
        return $this->belongsTo('App\Entities\User','groom');
    }
    
    public function brides(){
        return $this->belongsTo('App\Entities\User','bride');
    }
    
    public function branch(){
        return $this->belongsTo('App\Entities\Branch','branch_id');
    }

}
