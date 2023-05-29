<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WebFamilyCard.
 *
 * @package namespace App\Entities;
 */
class WebFamilyCard extends Model implements Transformable
{
    use TransformableTrait, RelationshipsTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['branch_id', 'no_kk', 'address'];

    public function branch(){
        return $this->belongsTo('App\Entities\Branch', 'branch_id');
    }

    public function webUsers(){
        return $this->hasMany('App\Entities\WebUser', 'web_user_family_card_id');
    }

}
