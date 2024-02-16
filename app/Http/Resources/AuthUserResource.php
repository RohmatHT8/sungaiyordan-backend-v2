<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
            'nik' => $this->nik,
            'permissions' => $this->permissions,
            'widget_permissions' => $this->widgetPermissions,
            'widgets' => UserWidgetResource::collection($this->widgets()->with('widget')->get()),
            'report_permissions' => $this->reportPermissions,
            'main_branch' => new BranchResource($this->mainBranch),
            'branches' => BranchSelect::collection($this->branches),
        ];
    }
}
