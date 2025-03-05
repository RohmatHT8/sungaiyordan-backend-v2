<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
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
                    'nik' => $model->nik,
                    'name' => $model->name,
                    'phone_number' => $model->phone_number,
                    'branch' => !empty($model->mainBranch) ? $model->mainBranch->name:'',
                    'can_update' => $model->can_update,
                    'can_delete' => $model->can_delete,
                ];
            })
        ];
    }
}
