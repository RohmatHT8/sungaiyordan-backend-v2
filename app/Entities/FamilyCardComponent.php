<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FamilyCardComponent.
 *
 * @package namespace App\Entities;
 */
class FamilyCardComponent extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['family_card_id','user_id','sequence','no_kk_per_user','valid_until', 'status'];
    public $timestamps = false;

    public function familyCard() {
        return $this->belongsTo('App\Entities\FamilyCard','family_card_id');
    }

    public function user() {
        return $this->belongsTo('App\Entities\User','user_id');
    }

}
