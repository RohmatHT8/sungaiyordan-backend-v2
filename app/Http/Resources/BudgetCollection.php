<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BudgetCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function($model){
                return [
                    'id' => $model->id,
                    'note' => $model->note,
                    'amount' => $model->amount,
                    'date' => $model->date,
                    'role_name' => $model->role->name,
                    'branch_name' => $model->branch->name,
                    'is_closed' => !$model->is_closed ? 'Open' : 'Closed',
                    'can_delete' => $model->can_delete,
                    'can_update' => $model->can_update,
                ];
            })
        ];
    }
}
