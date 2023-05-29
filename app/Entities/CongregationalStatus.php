<?php

namespace App\Entities;

use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CongregationalStatus.
 *
 * @package namespace App\Entities;
 */
class CongregationalStatus extends Model implements Transformable
{
    use TransformableTrait,TransactionLogModelTrait;

    protected $fillable = ['status','date','note','user_id'];
    public $timestamps = false;

    public function user() {
        return $this->belongsTo('App\Entities\user','user_id');
    }

}
