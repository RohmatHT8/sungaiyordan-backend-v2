<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class FamilyCardCollection extends ResourceCollection
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
                    'no' => $model->no,
                    'head_of_family' => !empty($model->components()->where('status','Kepala Keluarga')->first()->user) ? $model->components()->where('status','Kepala Keluarga')->first()->user->name:NULL,
                    'branch_name' => $model->branch->name,
                    'count' => $model->components->count(),
                    'can_delete' => true,
                ];
            })
        ];
    }
}
