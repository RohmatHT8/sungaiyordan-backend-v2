<?php

namespace App\Http\Requests;

use App\Rules\isDuplicateUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class FamilyCardCreateRequest extends FormRequest
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
            'no' => 'nullable|unique:family_cards,no',
            'address' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'postal_code' => 'required',
            'rtrw' => 'required',
            'users' => ['required','array', new isDuplicateUser],
            'users.*.user_id' => 'required|exists:users,id',
            'users.*.status' => 'required|string',
            'users.*.valid_until' => 'nullable|string|date_format:Y-m-d|before_or_equal:today',
            'users.*.sequence' => 'required|distinct|integer',
        ];
    }
}
