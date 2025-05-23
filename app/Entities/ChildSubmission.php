<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ChildSubmission.
 *
 * @package namespace App\Entities;
 */
class ChildSubmission extends Model implements Transformable
{
    use TransformableTrait, SoftDeletes, TransactionLogModelTrait;

    protected $fillable = ['no', 'date', 'branch_id', 'user_id', 'who_blessed', 'who_signed'];

    protected $append = ['can_delete', 'can_update', 'can_print'];

    public function user()
    {
        return $this->belongsTo('App\Entities\User', 'user_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Entities\Branch', 'branch_id');
    }

    public function getCanPrintAttribute()
    {
        return $this->defaultCanPrintAttribute();
    }

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute();
    }

    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute();
    }
}
