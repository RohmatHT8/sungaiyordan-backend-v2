<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyCardResource extends JsonResource
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
            'branch' => new BranchSelect($this->whenLoaded('branch')),
            'no' => $this->no,
            'address' => $this->address,
            'city' => $this->city,
            'postal_code' => $this->postal_code,
            'rtrw' => $this->rtrw,
            'subdistrict' => $this->subdistrict,
            'users' => FamilyCardComponentResource::collection($this->whenLoaded('components')),
            'can_delete' => $this->can_delete
        ];
    }
}
