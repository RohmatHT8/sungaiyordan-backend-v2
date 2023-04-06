<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserRole.
 *
 * @package namespace App\Entities;
 */
class UserRole extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','role_id','valid_from'];
    public $timestamps = false;

    public function user(){
        return $this->belongsTo('App\Entities\User')->withTrashed();
    }

    public function role(){
        return $this->belongsTo('App\Entities\Role')->withTrashed();
    }

}
