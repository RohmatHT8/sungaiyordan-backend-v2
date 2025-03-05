<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BranchCollection extends ResourceCollection
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
                    'name' => $model->name,
                    'code' => $model->code,
                    'address' => $model->address,
                    'shepherd_name' => $model->shepherd->name,
                    'telephone'=> $model->telephone,
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                ];
            })
        ];
    }
}
