<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BookingRoomResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => new UserSelect($this->whenLoaded('userOwn')),
            'branch_id' => new BranchSelect($this->whenLoaded('branch')),
            'user' => $this->user,
            'whereof' => $this->whereof,
            'date' => $this->date,
            'date_until' => $this->date_until,
            'used_for' => $this->used_for,
            'is_roxy' => $this->user ? 'Tidak' : 'Ya',
            'can_update' => $this->can_update,
            'can_print' => $this->can_print
        ];
    }
}
