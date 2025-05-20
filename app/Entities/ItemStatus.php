<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ItemStatus.
 *
 * @package namespace App\Entities;
 */
class ItemStatus extends Model implements Transformable
{
    use TransformableTrait, TransactionLogModelTrait;

    protected $fillable = ['status', 'date', 'note', 'item_id', 'room_id'];
    public $timestamps = false;
    protected $appends = ['can_update', 'can_delete', 'can_print'];

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanPrintAttribute()
    {
        return $this->defaultCanPrintAttribute();
    }

    public function item()
    {
        return $this->belongsTo('App\Entities\item');
    }

    public function room()
    {
        return $this->belongsTo('App\Entities\room');
    }
}
