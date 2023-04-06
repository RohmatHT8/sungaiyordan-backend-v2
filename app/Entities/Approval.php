<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Approval.
 *
 * @package namespace App\Entities;
 */
class Approval extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['name','permission_id','branch_id','requirement','based_on'];
    public $timestamps = false;

    protected $appends = ['can_update','can_delete','can_print'];

    public function permission(){
        return $this->belongsTo('App\Entities\Permission');
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch')->withTrashed();
    }

    // public function specialRoles(){
    //     return $this->hasMany('App\Entities\ApprovalSpecialRole');
    // }

    public function roles(){
        return $this->hasMany('App\Entities\ApprovalRole');
    }

    public function levels(){
        return $this->hasMany('App\Entities\ApprovalLevel');
    }

    // public function priceTotals(){
    //     return $this->hasMany('App\Entities\ApprovalPriceTotal');
    // }

    // public function priceDiffs(){
    //     return $this->hasMany('App\Entities\ApprovalPriceDiff');
    // }

}
