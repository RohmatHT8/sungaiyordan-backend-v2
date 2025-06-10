<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FinanceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'note' => $this->note,
            'date' => $this->date,
            'status' => $this->status,
            'amount' => $this->amount,
            'balance' => $this->balance,
            'divisi' => $this->divisi,
            'branch_id' => new BranchSelect($this->whenLoaded('branch')),
            'role_id' => new RoleSelect($this->whenLoaded('role')),
            'can_update' => $this->can_update,
            'can_delete' => $this->can_delete,
        ];
    }
}
