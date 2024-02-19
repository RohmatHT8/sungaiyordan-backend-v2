<?php

namespace App\Entities;

use App\Util\Helper;
use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable implements Transformable
{
    use TransformableTrait, Notifiable, HasApiTokens, SoftDeletes, RelationshipsTrait, TransactionLogModelTrait;

    protected $fillable = ['name', 'email', 'password','nik','no_ktp','place_of_birth','date_of_birth','gender','ktp_address','address','phone_number','father','mother', 'main_branch_id', 'join_date','profession'];

    protected $dates = ['deleted_at'];

    protected $hidden = ['password', 'remember_token'];

    protected $appends = ['role_id','permissions', 'can_update', 'can_delete', 'can_approve', 'can_print','widget_permissions','report_permissions'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function hasAuthority($abilities){
        $permissions = Permission::whereIn('ability',explode('|',$abilities))->get();
        foreach ($permissions as $permission){
            if($permission->mappings()
            ->whereIn('branch_id',$this->branches()->pluck('branches.id')->all())
            ->whereIn('role_id',$this->getSubordinatesRoleId())->count()){
                return true;
            }
        }

        // if (PeriodPermission::whereHas('permission', function($q) use($abilities){
        //     $q->whereIn('ability', explode('|', $abilities));
        // })
        //     ->where('user_id',Auth::id())
        //     ->where('start_time', '<=', date('Y-m-d H:i'))
        //     ->where(function($q){
        //         $q->where('end_time', '>=', date('Y-m-d H:i'))
        //             ->orWhere(function($q){
        //                 $q->whereNotNull('duration');
        //                 $q->whereNull('end_time');
        //             });
        //     })
        //     ->where('need_approval', 0)->get()->count()) {
        //     return true;
        // }

        $reportPermissions = ReportPermission::whereIn('ability',explode('|',$abilities))->get();
        foreach ($reportPermissions as $reportPermission){
            if($reportPermission->mappings()
                ->whereIn('branch_id',$this->branches()->pluck('branches.id')->all())
                ->whereIn('role_id',$this->getSubordinatesRoleId())->count()){
                return true;
            }
        }

        $widgetPermissions = WidgetPermission::whereIn('ability',explode('|',$abilities))->get();
        foreach ($widgetPermissions as $widgetPermission){
            if($widgetPermission->mappings()
                ->whereIn('branch_id',$this->branches()->pluck('branches.id')->all())
                ->whereIn('role_id',$this->getSubordinatesRoleId())->count() ||
                $widgetPermission->widget()->where('default',true)->count()){
                return true;
            }
        }
        return false;
    }

    public function role(){
        return $this->belongsTo('App\Entities\Role');
    }

    public function roles(){
        return $this->belongsToMany('App\Entities\Role','user_roles')->withPivot('valid_from');
    }

    public function findForPassport($identifier) {
        return $this->orWhere('email', $identifier)->first();
    }

    public function getSubordinatesRoleId(){
        $roles = [$this->role_id];
        $rolePointer = 0;
        while($rolePointer<count($roles)){
            $roles = array_unique (array_merge ($roles, Role::where('boss_id',$roles[$rolePointer])->pluck('id')->all()));
            $rolePointer++;
        }
        return $roles;
    }

    public function getRoleIdAttribute()
    {
        return $this->getRoleId(date('Y-m-d'));
    }

    public function getRoleId($date){
        $role = $this->roles()->where('valid_from','<=',$date)->orderBy('valid_from','desc')->first();
        if(empty($role)){
            return null;
        }
        return $role->id;
    }

    public function getPermissionsAttribute(){
        if(Auth::user()->role_id == 1){
            return array_column(Permission::all()->toArray(), 'ability');
        }

        $permissions = Permission::whereHas('mappings',function ($mapping){
            $mapping->where('role_id',$this->role_id)
                ->whereIn('branch_id',Auth::user()->branches()->pluck('branches.id')->all());
        })->pluck('ability')->all();

        foreach ($permissions as $permission){
            $alias = Helper::getAliasAbility($permission,true);
            if(!in_array($alias,$permissions)){
                array_push($permissions,$alias);
            }
        }

        return $permissions;
    }

    public function getWidgetPermissionsAttribute(){
        if(Auth::user()->role_id == 1) {
            return array_column(WidgetPermission::all()->toArray(), 'ability');
        }

        return WidgetPermission::whereHas('mappings',function ($mapping){
            $mapping->whereIn('role_id',$this->getSubordinatesRoleId())
                ->whereIn('branch_id',Auth::user()->branches()->pluck('branches.id')->all());
        })->pluck('ability')->all();
    }

    public function widgets(){
        return $this->hasMany('App\Entities\UserWidget');
    }
    
    public function congregationStatuses() {
        return $this->hasMany('App\Entities\CongregationalStatus');
    }

    public function mainBranch(){
        return $this->belongsTo('App\Entities\Branch','main_branch_id')->withTrashed();
    }
    
    public function branches(){
        return $this->belongsToMany('App\Entities\Branch','user_branches')->withTrashed();
    }

    public function getCanDeleteAttribute(){
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanPrintAttribute(){
        return $this->defaultCanPrintAttribute();
    }

    public function getCanUpdateAttribute(){
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanApproveAttribute(){
        return true;
    }

    public function getReportPermissionsAttribute(){
        if(Auth::user()->role_id == 1) {
            return array_column(ReportPermission::all()->toArray(), 'ability');
        }

        return ReportPermission::whereHas('mappings',function ($mapping){
            $mapping->whereIn('role_id',$this->getSubordinatesRoleId())
                ->whereIn('branch_id',Auth::user()->branches()->pluck('branches.id')->all());
        })->pluck('ability')->all();
    }
}
