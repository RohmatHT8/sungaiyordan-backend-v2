<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class NumberSetting.
 *
 * @package namespace App\Entities;
 */
class NumberSetting extends Model implements Transformable
{
    use TransformableTrait,SoftDeletes,TransactionLogModelTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name','transaction_id','reset_type','need_approval'];
    protected $dates = ['deleted_at'];

    // protected $appends = ['can_approve','can_update','can_delete','can_print','approved_by'];

    public function transaction(){
        return $this->belongsTo('App\Entities\Transaction');
    }

    public function components(){
        return $this->hasMany('App\Entities\NumberSettingComponent');
    }
}
