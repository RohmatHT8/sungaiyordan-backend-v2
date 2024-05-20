<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'no' => 'nullable',
            'name' => 'required|max:255',
            'merk' => 'required|max:255',
            'item_type_id' => 'required|exists:item_types,id',
            'room_id' => 'required|exists:rooms,id',
            'branch_ids' => 'required|array',
            'branch_ids.*' => 'required|distinct|exists:branches,id,deleted_at,NULL',
            'date_buying' => 'required',
            'amount' => 'required'
        ];
    }
}
