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
            'branch_id' => new BranchSelect($this->whenLoaded('branch')),
            'no' => $this->no,
            'date' => $this->date,
            'who_blessed' => $this->who_blessed,
            'who_signed' => $this->who_signed,
            'user_id' => new UserSertificateSelect($this->whenLoaded('user')),
        ];
    }
}
