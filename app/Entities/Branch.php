<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Branch.
 *
 * @package namespace App\Entities;
 */
class Branch extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, RelationshipsTrait, TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code','name','address','telephone','shepherd_id','need_approval'];
    protected $date = ['deleted_at'];

    protected $appends = ['can_update','can_delete','can_print'];

    public function Shepherd(){
        return $this->belongsTo('App\Entities\User','shepherd_id')->withTrashed();
    }

    public function getCanUpdateAttribute() {
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanDeleteAttribute() {
        return true;
    }

    public function getCanPrintAttribute() {
        return $this->defaultCanPrintAttribute();
    }

}
