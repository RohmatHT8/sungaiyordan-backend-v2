<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Role.
 *
 * @package namespace App\Entities;
 */
class Role extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    protected $fillable = ['code','name','department_id','boss_id','need_approval'];
    protected $dates = ['deleted_at'];

    public function department(){
        return $this->belongsTo('App\Entities\Department');
    }

    public function userRoles(){
        return $this->hasMany('App\Entities\UserRole');
    }

    public function boss(){
        return $this->belongsTo('App\Entities\Role','boss_id');
    }

    public function permissionMappings(){
        return $this->hasMany('App\Entities\PermissionMapping');
    }

}
