<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class FinanceCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'note' => $model->note,
                    'date' => $model->date,
                    'status' => $model->status,
                    'amount' => $model->amount,
                    'balance' => $model->balance,
                    'can_update' => $model->can_update,
                    'can_delete' => $model->can_delete,
                ];
            })
        ];
    }
}
