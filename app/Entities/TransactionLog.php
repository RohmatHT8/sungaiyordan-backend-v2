<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TransactionLog.
 *
 * @package namespace App\Entities;
 */
class TransactionLog extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['permission_id','subject_id','causer_id','causer_type','previous_log_id','new_properties','method','is_active'];

    public function approvalLogs(){
        return $this->hasMany('App\Entities\ApprovalLog');
    }

    public function permission(){
        return $this->belongsTo('App\Entities\Permission');
    }

    public function previousLog(){
        return $this->belongsTo('App\Entities\TransactionLog','previous_log_id');
    }

    public function causer(){
        return $this->morphTo()->withTrashed();
//        return $this->belongsTo('App\Entities\User','causer_id')->withTrashed();
    }

}
