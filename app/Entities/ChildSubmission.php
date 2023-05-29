<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ChildSubmission.
 *
 * @package namespace App\Entities;
 */
class ChildSubmission extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, TransactionLogModelTrait;

    protected $fillable = ['no','date','branch_id','user_id','who_blessed'];

    protected $append = ['can_delete'];

    public function user(){
        return $this->belongsTo('App\Entities\User','user_id');
    }
    
    public function branch(){
        return $this->belongsTo('App\Entities\Branch','branch_id');
    }

}
