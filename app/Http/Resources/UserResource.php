<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'nik' => $this->nik,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth,
            'father' => $this->father,
            'mother' => $this->mother,
            'gender' => $this->gender,
            'join_date' => $this->join_date,
            'ktp_address' => $this->ktp_address,
            'address' => $this->address,
            'main_branch' => new BranchSelect($this->whenLoaded('mainBranch')),
            'no_ktp' => $this->no_ktp,
            'phone_no' => $this->phone_number,
            'profession' => $this->profession,
            'place_of_birth' => $this->place_of_birth,
            'branches' => BranchSelect::collection($this->whenLoaded('branches')),
            'need_approval' => $this->need_approval,
            'can_approve' => $this->can_approve,
            'can_update' => $this->can_update,
            'roles' => $this->whenPivotLoaded('roles','user_roles', function() {
                return UserRoleResource::collection($this->roles);
            }),
            'congregationalStatuses' => CongregationalSatatusResource::collection($this->whenLoaded('congregationStatuses'))
        ];
    }
}
