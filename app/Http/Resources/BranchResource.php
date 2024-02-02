<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'address' => $this->address,
            'telephone'=> $this->telephone,
            'user' => new UserSelect($this->whenLoaded('Shepherd')),
            'can_update' => $this->can_update,
            'can_print' => $this->can_print
        ];
    }
}
