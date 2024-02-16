<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ReportPermissionMapping.
 *
 * @package namespace App\Entities;
 */
class ReportPermissionMapping extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['report_permission_id','role_id','branch_id','report_permission_setting_id'];
    public $timestamps = false;

    public function reportPermission(){
        return $this->belongsTo('App\Entities\ReportPermission');
    }

    public function role(){
        return $this->belongsTo('App\Entities\Role')->withTrashed();
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch')->withTrashed();
    }

    public function setting(){
        return $this->belongsTo('App\Entities\ReportPermissionSetting');
    }

}
