<?php

namespace App\Http\Requests;

use App\Rules\NotDuplicateHOFAndWife;
use App\Rules\UniqueIf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;

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
            'users.*.nik' => 'required',
            'users.*.place_of_birth' => 'required',
            'users.*.date_of_birth' => 'required|date_format:Y-m-d|before:today',
            'users.*.join_date' => 'nullable|date_format:Y-m-d',
            'users.*.gender' => 'required',
            'users.*.congregational_status' => 'required',
            'users.*.status_baptize' => 'required_if:users.*.congregational_status,==,Berjemaat',
            'users.*.date_of_baptize' => 'nullable|date_format:Y-m-d',
            'users.*.status_shdr' => 'required_if:users.*.congregational_status,==,Berjemaat',
            'users.*.date_shdr' => 'nullable|date_format:Y-m-d',
            'users.*.profession' => 'required',
            'users.*.email' => 'nullable',
            'users.*.marital_status' => 'required',
            'users.*.wedding_date' => 'nullable|date_format:Y-m-d',
            'users.*.family_member_status' => ['required', new NotDuplicateHOFAndWife(collect($this->users)->pluck('family_member_status'))]
        ];
    }
}
