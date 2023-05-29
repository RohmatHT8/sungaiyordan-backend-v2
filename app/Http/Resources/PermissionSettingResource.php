<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PermissionSettingResource extends JsonResource
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
            'roles' => RoleSelect::collection($this->whenLoaded('roles'))->unique()->toArray(),
            'branches' => BranchSelect::collection($this->whenLoaded('branches'))->unique()->toArray(),
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions'))->unique()->toArray(),
            'need_approval' => $this->need_approval,
            'can_approve' => $this->can_approve,
            'can_update' => $this->can_update
        ];
    }
}
