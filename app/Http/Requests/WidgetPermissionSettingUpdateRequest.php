<?php

namespace App\Http\Requests;

use App\Rules\OwnBranch;
use App\Rules\ValidRoleBranchWidgetPermission;
use App\Util\Helper;
use Illuminate\Foundation\Http\FormRequest;

class WidgetPermissionSettingUpdateRequest extends FormRequest
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
        $rules = Helper::addTransactionAttributeRule([
            'role_ids' => 'required|array',
            'role_ids.*' => ['required','distinct','exists:roles,id,deleted_at,NULL,need_approval,0',
                new ValidRoleBranchWidgetPermission('role_id','branch_id',$this->request->all('branch_ids'),$this->id)],
            'branch_ids' => 'required|array',
            'branch_ids.*' => ['required','distinct','exists:branches,id,deleted_at,NULL,need_approval,0', new OwnBranch,
                new ValidRoleBranchWidgetPermission('branch_id','role_id',$this->request->all('role_ids'),$this->id)],
            'widget_permission_ids' => 'required|array',
            'widget_permission_ids.*' => ['required','distinct','exists:widget_permissions,id'],
        ],'WidgetPermissionSetting',$this->request);

        return Helper::mergeAttachmentRequest($rules);
    }
}
