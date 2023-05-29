<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NumberSettingResource extends JsonResource
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
            'transaction' => new TransactionResource($this->whenLoaded('transaction')),
            'reset_type' => $this->reset_type,
            'components' => NumberSettingComponentResource::collection($this->whenLoaded('components')),
            'need_approval' => $this->need_approval,
            'can_approve' => $this->can_approve,
            'can_update' => $this->can_update
        ];
    }
}
