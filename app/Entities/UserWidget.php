<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserWidget.
 *
 * @package namespace App\Entities;
 */
class UserWidget extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['user_id','widget_id','show','sequence'];
    public $timestamps = false;

    public function widget(){
        return $this->belongsTo('App\Entities\Widget');
    }

    public function user(){
        return $this->belongsTo('App\Entities\User')->withTrashed();
    }
}
