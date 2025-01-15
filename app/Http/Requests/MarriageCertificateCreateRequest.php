<?php

namespace App\Http\Requests;

use App\Rules\IsGender;
use App\Rules\IsWomen;
use Illuminate\Foundation\Http\FormRequest;

class MarriageCertificateCreateRequest extends FormRequest
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
            'groom' => ['required', 'exists:users,id', new IsGender('Laki-Laki')],
            'bride' => ['required', 'exists:users,id', new IsGender('Perempuan')],
            'branch_id' => 'nullable|exists:branches,id,deleted_at,NULL',
            'branch_non_local' => 'nullable',
            'no' => 'nullable|unique:marriage_certificates,no',
            'date' => 'required|date_format:Y-m-d',
            'who_blessed' => 'required|string',
            'who_signed' => 'required|string',
            'location' => 'required|string',
        ];
    }
}
