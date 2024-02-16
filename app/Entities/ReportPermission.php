<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ReportPermission.
 *
 * @package namespace App\Entities;
 */
class ReportPermission extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['ability','report_id'];
    public $timestamps = false;

    public function mappings(){
        return $this->hasMany('App\Entities\ReportPermissionMapping');
    }

    public function report(){
        return $this->belongsTo('App\Entities\Report');
    }

}
