<?php

namespace App\Http\Requests;

use App\Rules\IsExistUserId;
use Illuminate\Foundation\Http\FormRequest;

class ShdrCreateRequest extends FormRequest
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
            'user_id' => ['required','exists:users,id'],
            'place_of_shdr' => 'required|exists:branches,id,deleted_at,NULL',
            'no' => 'nullable|unique:shdrs,no',
            'date_shdr' => 'required|date_format:Y-m-d',
            'date_until' => 'required|date_format:Y-m-d',
            'who_signed' => 'required|string',
        ];
    }
}
