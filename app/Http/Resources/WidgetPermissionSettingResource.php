<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WidgetPermissionSettingResource extends JsonResource
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
            'widget_permissions' => WidgetPermissionResource::collection($this->whenLoaded('widgetPermissions'))->unique()->toArray(),
            // 'transaction_attributes' => TransactionAttributeValueResource::collection($this->whenLoaded('transactionAttributes')),
            'need_approval' => $this->need_approval,
            'can_approve' => $this->can_approve,
            'can_update' => $this->can_update
        ];
    }
}
