<?php

namespace App\Http\Requests;

use App\Rules\isDuplicateUser;
use Illuminate\Foundation\Http\FormRequest;

class FamilyCardUpdateRequest extends FormRequest
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
            'branch_id' => 'required|exists:branches,id',
            'no' => 'nullable|unique:family_cards,no,'.$this->id,
            'address' => 'required',
            'users' => ['required','array', new isDuplicateUser],
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.status' => 'required|string',
            'users.*.valid_until' => 'nullable|string|date_format:Y-m-d',
            'users.*.sequence' => 'required|distinct|integer',
        ];
    }
}
