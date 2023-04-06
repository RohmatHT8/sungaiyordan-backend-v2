<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ApprovalLog.
 *
 * @package namespace App\Entities;
 */
class ApprovalLog extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['transaction_log_id','approver_id'];

    public function transactionLog(){
        return $this->belongsTo('App\Entities\TransactionLog');
    }

    public function approver(){
        return $this->belongsTo('App\Entities\User','approver_id')->withTrashed();
    }

}
