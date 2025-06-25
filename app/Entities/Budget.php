<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Budget.
 *
 * @package namespace App\Entities;
 */
class Budget extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait, TransactionLogModelTrait;

    protected $fillable = ['note', 'amount', 'date', 'role_id', 'branch_id', 'is_closed'];

    protected $casts = [
        'is_closed' => 'boolean',
    ];

    protected $appends = ['can_update', 'can_delete', 'can_close'];

    public function getCanDeleteAttribute()
    {
        return $this->defaultCanDeleteAttribute() && !$this->is_closed;
    }

    public function getCanUpdateAttribute()
    {
        return $this->defaultCanUpdateAttribute()  && !$this->is_closed;
    }

    public function getCanCloseAttribute()
    {
        $user = auth()->user();
        if ($this->is_closed) {
            return false;
        }
        Log::info(in_array('finance-create', $user->permissions ?? []));
        return in_array('finance-create', $user->permissions ?? []);
    }

    public function role()
    {
        return $this->belongsTo('App\Entities\Role', 'role_id');
    }

    public function branch()
    {
        return $this->belongsTo('App\Entities\Branch', 'branch_id');
    }
}
