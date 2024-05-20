<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Building.
 *
 * @package namespace App\Entities;
 */
class Building extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'location'];
    protected $appends = ['can_update', 'can_delete', 'can_print'];

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
