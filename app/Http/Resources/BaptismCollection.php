<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BaptismCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function($model){
                return [
                    'id' => $model->id,
                    'name' => $model->user->name,
                    'who_baptism' => $model->who_baptism,
                    'place_baptism' => !empty($model->branch->name) ? $model->branch->name : $model->place_of_baptism_outside,
                    'date' => $model->date,
                    'no' => $model->no,
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                ];
            })
        ];
    }
}
