<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class NumberSettingCollection extends ResourceCollection
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
                    'transaction' => $model->transaction->name,
                    'reset_type' => $model->reset_type,
                    'need_approval' => $model->need_approval,
                    'approved_by' => $model->approved_by,
                    'can_approve' => $model->can_approve,
                    'can_delete' => $model->can_delete,
                    'can_print' => $model->can_print,
                    'can_duplicate' => true
                ];
            })
        ];
    }
}
