<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BookingRoomCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function($model){
                return [
                    'id' => $model->id,
                    'used_for' => $model->used_for,
                    'user' => !empty($model->user->name) ? $model->userOwn->name : $model->user,
                    'whereof' => !empty($model->branch->name) ? $model->branch->name : $model->whereof,
                    'date' => $model->date,
                    'date_until' => $model->date_until,
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                ];
            })
        ];
    }
}
