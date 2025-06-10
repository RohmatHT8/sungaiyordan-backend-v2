<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinanceUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'note' => 'required',
            'date' => 'required|date_format:Y-m-d',
            'status' => 'required',
            'amount' => 'required',
            'balance' => 'required',
            'divisi' => $this->is_kadiv == 'Tidak' ? 'required' : 'nullable',
            'branch_id' =>  'required|exists:branches,id',
            'role_id' => $this->is_kadiv == 'Ya' ? 'required|exists:roles,id' : 'nullable|exists:roles,id'
        ];
    }
}
