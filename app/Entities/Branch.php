<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Branch.
 *
 * @package namespace App\Entities;
 */
class Branch extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code','name','address','telephone','shepherd_id','need_approval'];
    protected $date = ['deleted_at'];

    protected $appends = ['can_approve','can_update','can_delete','can_print','approved_by'];

    public function Shepherd(){
        return $this->belongsTo('App\Entities\User','shepherd_id')->withTrashed();
    }

    public function getCanApproveAttribute() {
        return true;
    }

    public function getCanUpdateAttribute() {
        return true;
    }

    public function getCanDeleteAttribute() {
        return true;
    }

    public function getCanPrintAttribute() {
        return true;
    }

    public function getApprovedByAttribute() {
        return true;
    }

}
