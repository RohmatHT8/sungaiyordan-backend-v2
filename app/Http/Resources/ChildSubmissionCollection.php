<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ChildSubmissionCollection extends ResourceCollection
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
            'data' =>  $this->collectResource($this->collection)->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'name' => $model->user->name,
                    'who_blessed' => $model->who_blessed,
                    'branch_name' => $model->branch->name,
                    'date' => $model->date,
                    'no' => $model->no,
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                    'can_print' => $model->can_print,
                ];
            })
        ];
    }
}
