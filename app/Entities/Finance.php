<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Prettus\Repository\Traits\TransformableTrait;

class Finance extends Model
{
    use TransformableTrait, TransactionLogModelTrait, RelationshipsTrait;

    protected $fillable = ['note', 'date', 'status', 'amount', 'balance', 'divisi', 'role_id', 'branch_id'];
    protected $dates = ['deleted_at'];
    protected $appends = ['can_update', 'can_delete', 'can_print', 'last_balance'];

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

    public function getLastBalanceAttribute()
    {
        $lastFinance = self::orderByDesc('date')->first();
        return $lastFinance ? $lastFinance->balance : null;
    }

    public function role()
    {
        return $this->belongsTo('App\Entities\Role');
    }

    public function branch()
    {
        return $this->belongsTo('App\Entities\Branch');
    }
}
