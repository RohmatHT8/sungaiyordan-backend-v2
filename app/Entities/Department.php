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

    protected $append = ['can_update', 'can_delete'];

    public function roles(){
        return $this->hasMany('App\Entities\Role');
    }
}
