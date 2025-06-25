<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BudgetResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'note' => $this->note,
            'amount' => $this->amount,
            'date' => $this->date,
            'role_id'=> new RoleSelect($this->whenLoaded('role')),
            'branch_id' => new BranchSelect($this->whenLoaded('branch')),
            'is_closed' => $this->is_closed,
            'can_update' => $this->can_update,
            'can_print' => $this->can_print,
            'can_close' => $this->can_close
        ];
    }
}
