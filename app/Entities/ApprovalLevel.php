<?php

namespace App\Entities;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class ApprovalLevel.
 *
 * @package namespace App\Entities;
 */
class ApprovalLevel extends Model implements Transformable
{
    use TransformableTrait;

    protected $fillable = ['approval_id','level_diff','level_count'];
    public $timestamps = false;

    public function approval(){
        return $this->belongsTo('App\Entities\Approval');
    }

}
