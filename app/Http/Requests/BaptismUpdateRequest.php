<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BaptismUpdateRequest extends FormRequest
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
            'user_id' => ['required','exists:users,id', 'unique:baptisms,user_id,'.$this->id],
            'place_of_baptism_inside' => 'nullable|exists:branches,id,deleted_at,NULL',
            'no' => 'nullable|unique:baptisms,no,'.$this->id,
            'date' => 'required|date_format:Y-m-d|before_or_equal:today',
            'who_baptism' => 'required|string',
        ];
    }
}
