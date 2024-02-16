<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ReportPermissionSetting.
 *
 * @package namespace App\Entities;
 */
class ReportPermissionSetting extends Model implements Transformable
{
    use TransformableTrait,TransactionLogModelTrait,RelationshipsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['need_approval'];

    protected $appends = ['can_update','can_delete'];

    public function reportPermissions(){
        return $this->belongsToMany('App\Entities\ReportPermission','report_permission_mappings','report_permission_setting_id','report_permission_id');
    }

    public function roles(){
        return $this->belongsToMany('App\Entities\Role','report_permission_mappings','report_permission_setting_id','role_id')->withTrashed();
    }

    public function branches(){
        return $this->belongsToMany('App\Entities\Branch','report_permission_mappings','report_permission_setting_id','branch_id')->withTrashed();
    }

    public function getCanUpdateAttribute()
    {
        return true;
    }

    public function getCanDeleteAttribute()
    {
        return true;
    }

}
