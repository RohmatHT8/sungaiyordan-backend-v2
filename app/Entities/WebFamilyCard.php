<?php

namespace App\Entities;

use App\Util\RelationshipsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
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
    protected $appends = ['can_convert'];

    public function getCanConvertAttribute() {
        $niks = [];
        foreach($this->webUsers()->get('nik') as $nik) {
            array_push($niks,$nik['nik']);
        }
        return !empty(User::whereIn('no_ktp', $niks)->count());
    }

    public function branch(){
        return $this->belongsTo('App\Entities\Branch', 'branch_id');
    }

    public function webUsers(){
        return $this->hasMany('App\Entities\WebUser', 'web_user_family_card_id');
    }

}
