<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ItemType.
 *
 * @package namespace App\Entities;
 */
class ItemType extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code','name'];
    protected $appends = ['can_update', 'can_delete'];

    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute();
    }

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute();
    }


}
