<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ItemStatusCollection extends ResourceCollection
{
     public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function ($model) {
                return [
                    'id' => $model->id,
                    'status' => $model->status,
                    'date' => $model->date,
                    'note' => $model->note,
                    'room' => $model->room ? $model->room->name : "-",
                    'item' => $model->item->no .' - '. $model->item->name,
                    'can_update' => $model->can_update,
                    'can_delete' => $model->can_delete,
                    'can_print' => $model->can_print,
                ];
            })
        ];
    }
}
