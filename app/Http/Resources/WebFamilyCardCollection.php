<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WebFamilyCardCollection extends ResourceCollection
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
                    'no_kk' => $model->no_kk,
                    'address' => $model->address,
                    'branch_name' => $model->branch->name,
                    'head_of_family' => !empty($model->webUsers()->where('family_member_status','Kepala Keluarga')->first()->name) ? $model->webUsers()->where('family_member_status','Kepala Keluarga')->first()->name:NULL,
                    'count' => $model->webUsers->count(),
                ];
            })
        ];
    }
}
