<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Item.
 *
 * @package namespace App\Entities;
 */
class Item extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, TransactionLogModelTrait, RelationshipsTrait;

    protected $fillable = ['no', 'name', 'merk', 'item_type_id', 'amount', 'price', 'note', 'room_id', 'date_buying'];
    protected $dates = ['deleted_at'];
    protected $appends = ['can_update', 'can_delete', 'can_print'];

    public function getCanUpdateAttribute () {
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanDeleteAttribute () {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanPrintAttribute () {
        return $this->defaultCanPrintAttribute();
    }

    public function itemType(){
        return $this->belongsTo('App\Entities\ItemType');
    }

    public function room(){
        return $this->belongsTo('App\Entities\Room');
    }

    public function branches(){
        return $this->belongsToMany('App\Entities\Branch','item_branches')->withTrashed();
    }
}
