<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyCardComponentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user' => new UserSelect($this->whenLoaded('user')),
            'sequence' => $this->sequence,
            'no_kk_per_user' => $this->no_kk_per_user,
            'valid_until' => $this->valid_until,
            'status' => $this->status,
            'can_delete' => true
        ];
    }
}
