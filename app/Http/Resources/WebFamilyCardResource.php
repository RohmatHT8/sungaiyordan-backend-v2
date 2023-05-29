<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebFamilyCardResource extends JsonResource
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
            'no_kk' => $this->no_kk,
            'address' => $this->address,
            'users' => WebUserResource::collection($this->whenLoaded('WebUsers')),
            'can_delete' => true
        ];
    }
}
