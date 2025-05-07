<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Department.
 *
 * @package namespace App\Entities;
 */
class Department extends Model implements Transformable
{
    use TransformableTrait, TransactionLogModelTrait;

    protected $fillable = ['code','name','need_approval'];
    protected $dates = ['deleted_at'];

    protected $appends = ['can_update', 'can_delete', 'can_print'];

    public function roles(){
        return $this->hasMany('App\Entities\Role');
    }

    public function getCanUpdateAttribute () {
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanDeleteAttribute () {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanPrintAttribute () {
        return $this->defaultCanPrintAttribute();
    }
}
