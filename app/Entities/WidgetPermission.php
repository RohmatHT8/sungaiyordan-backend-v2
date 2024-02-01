<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WidgetPermission.
 *
 * @package namespace App\Entities;
 */
class WidgetPermission extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ability','widget_id'];
    public $timestamps = false;

    public function mappings(){
        return $this->hasMany('App\Entities\WidgetPermissionMapping');
    }

    public function widget(){
        return $this->belongsTo('App\Entities\Widget');
    }

}
