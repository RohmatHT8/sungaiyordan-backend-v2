<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Transaction.
 *
 * @package namespace App\Entities;
 */
class Transaction extends Model implements Transformable
{
    use TransformableTrait;
    protected $fillable = ['name','subject'];
    public $timestamps = false;
    
    public function permissions(){
        return $this->hasMany('App\Entities\Permission');
    }

    public function attributes(){
        return $this->hasMany('App\Entities\TransactionAttribute');
    }

}
