<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserBranch.
 *
 * @package namespace App\Entities;
 */
class UserBranch extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['user_id','branch_id'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\Entities\User')->withTrashed();
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch')->withTrashed();
    }

}
