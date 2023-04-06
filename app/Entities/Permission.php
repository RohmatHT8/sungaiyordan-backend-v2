<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Permission.
 *
 * @package namespace App\Entities;
 */
class Permission extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ability','transaction_id'];
    public $timestamps = false;

    public function mappings(){
        return $this->hasMany('App\Entities\PermissionMapping');
    }

    public function transaction(){
        return $this->belongsTo('App\Entities\Transaction');
    }

    // public function periods(){
    //     return $this->hasMany('App\Entities\PeriodPermission');
    // }

}
