<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ConfirmationOfMarriageResource extends JsonResource
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
            'branch_non_local' => $this->branch_non_local,
            'date' => $this->date,
            'who_blessed' => $this->who_blessed,
            'groom' => new UserSertificateSelect($this->whenLoaded('grooms')),
            'bride' => new UserSertificateSelect($this->whenLoaded('brides')),
            'location' => $this->location,
            'can_delete' => true,
            'can_print' => true
        ];
    }
}
