<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BranchUpdateRequest extends FormRequest
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
            'code' => 'required|unique:branches,code,'.$this->id.'NULL,id,deleted_at,NULL',
            'address' => 'required',
            'shepherd_id' => 'required|exists:users,id,deleted_at,NULL',
            'telephone' => 'required',
        ];
    }
}
