<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ItemStatusUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'status' => 'required',
            'note' => 'nullable|max:255',
            'item_id' => 'required|exists:item_types,id',
            'room_id' => 'nullable|exists:rooms,id',
            'date' => 'required',
        ];
    }
}
