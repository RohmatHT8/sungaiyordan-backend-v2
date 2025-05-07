<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MarriageCertificateCollection extends ResourceCollection
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
                    'groom' => $model->grooms->name,
                    'bride' => $model->brides->name,
                    'who_blessed' => $model->who_blessed,
                    'branch' => $model->branch_id ? $model->branch->name : $model->branch_non_local,
                    'date' => $model->date,
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                    'can_print' => $model->can_print,
                ];
            })
        ];
    }
}
