<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BookingRoom.
 *
 * @package namespace App\Entities;
 */
class BookingRoom extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    protected $fillable = ['user_id', 'branch_id', 'user', 'whereof', 'date', 'date_until', 'used_for'];
    protected $appends = ['can_update', 'can_delete', 'can_print'];

    public function userOwn()
    {
        return $this->belongsTo('App\Entities\User', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Entities\Branch', 'branch_id');
    }

    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanPrintAttribute()
    {
        return $this->defaultCanPrintAttribute();
    }

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute();
    }
}
