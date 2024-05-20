<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'no' => $this->no,
            'name' => $this->name,
            'merk' => $this->merk,
            'item_type' => new ItemTypeSelect($this->whenLoaded('itemType')),
            'branches' => BranchSelect::collection($this->whenLoaded('branches')),
            'amount' => $this->amount,
            'price' => $this->price,
            'room' => new RoomSelect($this->whenLoaded('room')),
            'note' => $this->note,
            'date_buying' => $this->date_buying,
            'can_update' => $this->can_update,
            'can_delete' => $this->can_delete,
        ];
    }
}
