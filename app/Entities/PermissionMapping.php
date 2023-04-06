<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PermissionMapping.
 *
 * @package namespace App\Entities;
 */
class PermissionMapping extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['permission_id','role_id','branch_id','permission_setting_id'];
    public $timestamps = false;

    public function permission(){
        return $this->belongsTo('App\Entities\Permission');
    }

    public function role(){
        return $this->belongsTo('App\Entities\Role')->withTrashed();
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch')->withTrashed();
    }

    public function setting(){
        return $this->belongsTo('App\Entities\PermissionSetting');
    }

}
