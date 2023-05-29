<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use App\Util\TransactionLogModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Shdr.
 *
 * @package namespace App\Entities;
 */
class Shdr extends Model implements Transformable
{
    use TransformableTrait, TransactionLogModelTrait, RelationshipsTrait, SoftDeletes;

    protected $fillable = ['user_id','date_shdr','place_of_shdr','who_signed', 'no'];
    protected $append = ['can_delete'];

    public function user(){
        return $this->belongsTo('App\Entities\User','user_id');
    }
    
    public function branch(){
        return $this->belongsTo('App\Entities\Branch','place_of_shdr');
    }

}
