<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class PermissionSetting.
 *
 * @package namespace App\Entities;
 */
class PermissionSetting extends Model implements Transformable
{
    use TransformableTrait, TransactionLogModelTrait;

    protected $fillable = ['need_approval'];

    protected $appends = ['can_approve','can_update','can_delete','can_print','approved_by'];

    public function permissions(){
        return $this->belongsToMany('App\Entities\Permission','permission_mappings','permission_setting_id','permission_id');
    }

    public function roles(){
        return $this->belongsToMany('App\Entities\Role','permission_mappings','permission_setting_id','role_id');
    }

    public function branches(){
        return $this->belongsToMany('App\Entities\Branch','permission_mappings','permission_setting_id','branch_id');
    }

}
