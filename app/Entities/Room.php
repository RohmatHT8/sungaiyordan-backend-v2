<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Room.
 *
 * @package namespace App\Entities;
 */
class Room extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'name', 'note', 'building_id'];
    protected $appends = ['can_update', 'can_delete'];

    public function building() 
    {
        return $this->belongsTo('App\Entities\Building', 'building_id');
    }
    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute();
    }
}
