<?php

namespace App\Http\Requests;

use App\Rules\NotPresent;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest
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
            'email' => 'nullable|email|unique:users,email,NULL,id,deleted_at,NULL',
            'nik' => 'nullable|unique:users,nik',
            'password' => ['sometimes',new NotPresent],
            'no_ktp' => 'nullable|unique:users,no_ktp,NULL,id,deleted_at,NULL',
            'date_of_birth' => 'required|date_format:Y-m-d|before:today',
            'place_of_birth' => 'required',
            'ktp_address' => 'nullable',
            'address' => 'nullable',
            'phone_number' => 'nullable',
            'father' => 'required',
            'mother' => 'required',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'required|distinct|exists:branches,id,deleted_at,NULL',
            'main_branch_id' => 'required|exists:branches,id,deleted_at,NULL',
            'join_date' => 'required|date_format:Y-m-d',
            'congregationalStatuses' => 'required|array',
            'congregationalStatuses.*.status' => 'required|string',
            'congregationalStatuses.*.date' => 'nullable|date_format:Y-m-d',
            'congregationalStatuses.*.notes' => 'nullable|string',
        ];
    }
}
