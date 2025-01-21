<?php

namespace App\Http\Requests;

use App\Entities\Baptism;
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
            'user_id' => ['required', 'exists:users,id', 'unique:baptisms,user_id,' . $this->id],
            'place_of_baptism_inside' => 'nullable|exists:branches,id,deleted_at,NULL',
            'no' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== '000000' && Baptism::where('no', $value)->where('id', '!=', $this->id)->exists()) {
                        $fail('Nomor sudah digunakan.');
                    }
                },
            ],
            'date' => 'required|date_format:Y-m-d',
            'who_baptism' => 'required|string',
            'who_signed' => 'required|string',
        ];
    }
}
