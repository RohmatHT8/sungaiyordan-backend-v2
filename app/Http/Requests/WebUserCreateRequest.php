<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WebUserCreateRequest extends FormRequest
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

    public function rules()
    {
        return [
            'branch_id' => 'required|distinct|exists:branches,id,deleted_at,NULL',
            'no_kk' => 'nullable|string|unique:web_family_cards,no_kk',
            'address' => 'required',
            'users' => 'required|array',
            'users.*.name' => 'required',
            'users.*.father' => 'required',
            'users.*.mother' => 'required',
            'users.*.phone_number' => 'nullable',
            'users.*.nik' => 'required|unique:web_users,nik',
            'users.*.place_of_birth' => 'required',
            'users.*.date_of_birth' => 'required|date_format:Y-m-d|before:today',
            'users.*.join_date' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'users.*.gender' => 'required',
            'users.*.congregational_status' => 'required',
            'users.*.status_baptize' => 'required',
            'users.*.date_of_baptize' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'users.*.status_shdr' => 'required',
            'users.*.date_shdr' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'users.*.profession' => 'required',
            'users.*.email' => 'nullable|unique:web_users,email',
            'users.*.marital_status' => 'required',
            'users.*.wedding_date' => 'nullable|date_format:Y-m-d|before_or_equal:today'
        ];
    }
}
