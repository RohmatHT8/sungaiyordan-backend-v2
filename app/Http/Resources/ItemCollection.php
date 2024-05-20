<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Log;

class ItemCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'no' => $model->no,
                    'name' => $model->name,
                    'merk' => $model->merk,
                    'item_type' => $model->itemType->name,
                    'amount' => $model->amount,
                    'price' => $model->price,
                    'note' => $model->note,
                    'branches' => $model->branches()->pluck('name')->all(),
                    'can_update' => $model->can_update,
                    'can_delete' => $model->can_delete,
                ];
            })
        ];
    }
}
