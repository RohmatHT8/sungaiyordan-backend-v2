<?php

namespace App\Http\Requests;

use App\Entities\MarriageCertificate;
use App\Rules\IsGender;
use Illuminate\Foundation\Http\FormRequest;

class MarriageCertificateUpdateRequest extends FormRequest
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
            'no' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== '000000' && MarriageCertificate::where('no', $value)->where('id', '!=', $this->id)->exists()) {
                        $fail('Nomor sudah digunakan.');
                    }
                },
            ],
            'date' => 'required|date_format:Y-m-d',
            'who_blessed' => 'required|string',
            'who_signed' => 'required|string',
            'location' => 'required|string',
        ];
    }
}
