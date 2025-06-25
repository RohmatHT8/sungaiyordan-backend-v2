<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingRoomUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'used_for' => 'required',
            'user_id' => $this->is_kadiv == 'Ya' ? 'required|exists:users,id' : 'nullable|exists:users,id',
            'branch_id' => $this->is_kadiv == 'Ya' ? 'required|exists:branches,id' : 'nullable|exists:branches,id',
            'user' => $this->is_kadiv == 'Tidak' ? 'required' : 'nullable',
            'where_of' => $this->is_kadiv == 'Tidak' ? 'required' : 'nullable',
            'date' => [
                'required',
                'date_format:Y-m-d H:i:s',
                'after_or_equal:' . now()->format('Y-m-d H:i:s'),
            ],
            'date_until' => [
                'required',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    try {
                        $start = \Carbon\Carbon::parse($this->date);
                        $end = \Carbon\Carbon::parse($value);

                        if ($end->lte($start->copy()->addHour())) {
                            $fail('Tanggal akhir harus lebih dari 1 jam setelah tanggal mulai.');
                        }
                    } catch (\Exception $e) {
                        $fail('Format tanggal tidak valid.');
                    }
                }
            ]
        ];
    }
}
