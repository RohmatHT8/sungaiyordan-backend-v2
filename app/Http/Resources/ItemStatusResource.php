<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemStatusResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'status' => $this->status,
            'item_id' => new ItemSelect($this->whenLoaded('item')),
            'room_id' => new RoomSelect($this->whenLoaded('room')),
            'date' => $this->date,
            'note' => $this->note,
        ];
    }
}
