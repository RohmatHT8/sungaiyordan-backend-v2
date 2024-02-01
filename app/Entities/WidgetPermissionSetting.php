<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WidgetPermissionSetting.
 *
 * @package namespace App\Entities;
 */
class WidgetPermissionSetting extends Model implements Transformable
{
    use TransformableTrait,TransactionLogModelTrait,RelationshipsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['need_approval'];

    protected $appends = ['can_approve','can_update','can_delete','can_print','approved_by'];

    public function widgetPermissions(){
        return $this->belongsToMany('App\Entities\WidgetPermission','widget_permission_mappings','widget_permission_setting_id','widget_permission_id');
    }

    public function roles(){
        return $this->belongsToMany('App\Entities\Role','widget_permission_mappings','widget_permission_setting_id','role_id')->withTrashed();
    }

    public function branches(){
        return $this->belongsToMany('App\Entities\Branch','widget_permission_mappings','widget_permission_setting_id','branch_id')->withTrashed();
    }

}
