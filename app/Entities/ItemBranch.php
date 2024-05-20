<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ItemBranch.
 *
 * @package namespace App\Entities;
 */
class ItemBranch extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['item_id','branch_id'];
    public $timestamps = false;

    public function item(){
        return $this->belongsTo('App\Entities\Item')->withTrashed();
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch')->withTrashed();
    }

}
