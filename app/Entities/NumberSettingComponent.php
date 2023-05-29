<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class NumberSettingComponent.
 *
 * @package namespace App\Entities;
 */
class NumberSettingComponent extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['number_setting_id','sequence','type','format'];
    public $timestamps = false;

    public function numberSetting(){
        return $this->belongsTo('App\Entities\NumberSetting')->withTrashed();
    }

}
