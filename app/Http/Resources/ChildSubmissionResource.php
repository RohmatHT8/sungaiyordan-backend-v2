<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ChildSubmissionResource extends JsonResource
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
            'date' => $this->date,
            'who_blessed' => $this->who_blessed,
            'user' => new UserSertificateSelect($this->whenLoaded('user')),
            'can_delete' => true,
            'can_print' => true
        ];
    }
}
