<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

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
                    'branch' => $model->mainBranch->name,
                    'need_approval' => $model->need_approval,
                    'can_approve' => $model->can_approve,
                    'can_set_quit' => $model->can_set_quit,
                    'can_delete' => $model->can_delete,
                    'can_print' => $model->can_print
                ];
            })
        ];
    }
}
