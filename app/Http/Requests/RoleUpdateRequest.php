<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleUpdateRequest extends FormRequest
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

    public function rules()
    {
        $rules = [
            'name' => 'required',
            'code' => ['required','unique:roles,code,'.$this->id.',id,deleted_at,NULL'],
            'boss_id' => 'nullable|exists:roles,id,deleted_at,NULL,need_approval,0',
            'department_id' => ['required','exists:departments,id,deleted_at,NULL,need_approval,0']
        ];

        return $rules;
    }
}
