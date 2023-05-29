<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ShdrResource extends JsonResource
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
            'branch' => new BranchSelect($this->whenLoaded('branch')),
            'no' => $this->no,
            'date_shdr' => $this->date_shdr,
            'who_signed' => $this->who_signed,
            'user' => new UserSertificateSelect($this->whenLoaded('user')),
            'can_delete' => true
        ];
    }
}
