<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShdrUpdateRequest extends FormRequest
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
            // 'user_id' => ['required','exists:users,id', 'unique:shdrs,user_id,'.$this->id],
            'place_of_shdr' => 'required|exists:branches,id,deleted_at,NULL',
            'no' => 'nullable|unique:shdrs,no,'.$this->id,
            'date_shdr' => 'required|date_format:Y-m-d|before_or_equal:today',
            'who_signed' => 'required|string',
        ];
    }
}
