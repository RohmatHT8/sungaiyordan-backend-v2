<?php

namespace App\Http\Requests;

use App\Rules\NotPresent;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class UserUpdateRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email,'.$this->id,
            'nik' => 'required|unique:users,nik,'.$this->id,
            'password' => ['sometimes',new NotPresent],
            'no_ktp' => 'nullable|unique:users,no_ktp,'.$this->id.',id,deleted_at,NULL',
            'date_of_birth' => 'required|date_format:Y-m-d|before:today',
            'place_of_birth' => 'required',
            'ktp_address' => 'nullable',
            'address' => 'nullable',
            'pos_code' => 'nullable',
            'phone_number' => 'nullable',
            'father' => 'required',
            'mother' => 'required'
        ];
    }
}
