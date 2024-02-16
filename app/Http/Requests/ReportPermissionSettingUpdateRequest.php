<?php

namespace App\Http\Requests;

use App\Rules\OwnBranch;
use App\Rules\ValidRoleBranchReportPermission;
use App\Rules\ValidRoleBranchPermission;
use App\Util\Helper;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class ReportPermissionSettingUpdateRequest extends FormRequest
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
                new ValidRoleBranchReportPermission('role_id','branch_id',$this->request->all('branch_ids'),$this->id)],
            'branch_ids' => 'required|array',
            'branch_ids.*' => ['required','distinct','exists:branches,id,deleted_at,NULL,need_approval,0', new OwnBranch,
                new ValidRoleBranchReportPermission('branch_id','role_id',$this->request->all('role_ids'),$this->id)],
            'report_permission_ids' => 'required|array',
            'report_permission_ids.*' => ['required','distinct','exists:report_permissions,id'],
        ],'ReportPermissionSetting',$this->request);

        return $rules;
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = json_decode($validator->errors(),1);
        if(!empty($this->request->get('row_index'))){
            $errors['row_index'] = $this->request->get('row_index');
        }
        if(!empty($this->request->get('filename'))){
            $errors['filename'] = $this->request->get('filename');
        }

        throw (new ValidationException($validator))
            ->errorBag($this->errorBag)
            ->withMessages($errors)
            ->redirectTo($this->getRedirectUrl());
    }
}
