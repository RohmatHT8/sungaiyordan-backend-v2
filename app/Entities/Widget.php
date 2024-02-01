<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Widget.
 *
 * @package namespace App\Entities;
 */
class Widget extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','function','default','base_sequence'];

    public $timestamps = false;

    public function permissions() {
        return $this->hasMany('App\Entities\WidgetPermission');
    }

    public function userWidget() {
        return $this->hasMany('App\Entities\UserWidget');
    }

}
