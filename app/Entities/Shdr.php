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

    protected $fillable = ['user_id','date_shdr','date_until', 'place_of_shdr','who_signed', 'no'];
    protected $append = ['can_update', 'can_delete', 'can_print'];

    public function user(){
        return $this->belongsTo('App\Entities\User','user_id');
    }
    
    public function branch(){
        return $this->belongsTo('App\Entities\Branch','place_of_shdr');
    }

    public function getCanPrintAttribute() {
        return $this->defaultCanPrintAttribute() && $this->no !== '000000';
    }

    public function getCanUpdateAttribute() {
        return $this->defaultCanUpdateAttribute() && $this->no !== '000000';
    }

    public function getCanDeleteAttribute() {
        return $this->defaultCanDeleteAttribute();
    }

}
