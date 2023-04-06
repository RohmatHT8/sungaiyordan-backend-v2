<?php

namespace App\Http\Requests;

use App\Rules\ValidOldPassword;
use Illuminate\Foundation\Http\FormRequest;

class UserPasswordUpdateRequest extends FormRequest
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
            'old_password' => ['required', new ValidOldPassword],
            'password' => 'required|confirmed|min:8'
        ];
    }
}
