<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class WebUser.
 *
 * @package namespace App\Entities;
 */
class WebUser extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['web_user_family_card_id', 'name', 'father', 'mother', 'email', 'phone_number', 'nik', 'place_of_birth', 'date_of_birth', 'join_date', 'gender', 'congregational_status', 'status_baptize', 'date_of_baptize', 'place_of_baptize', 'who_baptize', 'status_shdr', 'date_shdr', 'place_of_shdr', 'profession', 'ktp_address', 'martial_status', 'wedding_date', 'place_of_wedding', 'married_church', 'who_married'];

    public function webUserFamilyCard() {
        return $this->belongsTo('App\Entities\WebFamilyCard', 'web_user_family_card_id');
    }

}
