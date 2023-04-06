<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ApprovalRole.
 *
 * @package namespace App\Entities;
 */
class ApprovalRole extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['approval_id','approver_id','sequence'];
    public $timestamps = false;

    public function approval(){
        return $this->belongsTo('App\Entities\Approval');
    }

    public function approver(){
        return $this->belongsTo('App\Entities\Role','approver_id')->withTrashed();
    }

}
