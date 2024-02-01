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
            'date_until' => $this->date_until,
            'who_signed' => $this->who_signed,
            'user' => new UserSertificateSelect($this->whenLoaded('user')),
            'can_print' => $this->can_print,
        ];
    }
}
