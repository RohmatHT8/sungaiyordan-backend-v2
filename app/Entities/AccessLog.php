<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class AccessLog.
 *
 * @package namespace App\Entities;
 */
class AccessLog extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id','request_url','duration'];

    public function user(){
        return $this->belongsTo('App\Entities\User');
    }

}
