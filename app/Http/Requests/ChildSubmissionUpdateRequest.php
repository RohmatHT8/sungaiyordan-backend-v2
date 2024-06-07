<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChildSubmissionUpdateRequest extends FormRequest
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
        return [
            'user_id' => 'required|exists:users,id',
            'branch_id' => 'nullable|exists:branches,id,deleted_at,NULL',
            'no' => 'nullable|unique:child_submissions,no,'.$this->id,
            'date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'who_blessed' => 'required|string',
            'who_signed' => 'required|string',
        ];
    }
}
