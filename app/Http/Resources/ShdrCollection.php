<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class ShdrCollection extends ResourceCollection
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
                    'who_signed' => $model->who_signed,
                    'place_shdr' => $model->branch->name,
                    'date_shdr' => $model->date_shdr,
                    'no' => $model->no,
                    'can_delete' => $model->can_delete,
                ];
            })
        ];
    }
}
