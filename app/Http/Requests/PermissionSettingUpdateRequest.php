<?php

namespace App\Http\Requests;

use App\Rules\OwnBranch;
use App\Rules\ValidRoleBranchPermission;
use App\Util\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class PermissionSettingUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'role_ids' => 'required|array',
            'role_ids.*' => ['required','distinct','exists:roles,id,deleted_at,NULL,need_approval,0',
                new ValidRoleBranchPermission('role_id','branch_id',$this->request->all('branch_ids'),$this->id)],
            'branch_ids' => 'required|array',
            'branch_ids.*' => ['required','distinct','exists:branches,id,deleted_at,NULL,need_approval,0',
                new ValidRoleBranchPermission('branch_id','role_id',$this->request->all('role_ids'),$this->id)],
            'permission_ids' => 'required|array',
            'permission_ids.*' => ['required','distinct','exists:permissions,id'],
        ];

        return $rules;
    }

}
