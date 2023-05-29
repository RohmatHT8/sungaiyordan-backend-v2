<?php

namespace App\Http\Requests;

use App\Rules\ValidComponentType;
use App\Rules\ValidNumberSettingComponent;
use App\Rules\ValidTransactionForNumbering;
use Illuminate\Foundation\Http\FormRequest;

class NumberSettingUpdateRequest extends FormRequest
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
        $rules = [
            'name' => 'required',
            'transaction_id' => ['required','exists:transactions,id',new ValidTransactionForNumbering($this->id)],
            'reset_type' => 'nullable|in:yearly,monthly,daily',
            'components' => ['required','array', new ValidNumberSettingComponent($this->request->get('reset_type'))],
            'components.*.sequence' => 'required|distinct|integer',
            'components.*.type' => ['required','in:text,year,month,day,counter,transaction-branch,transaction-department,warning-letter-type',
                new ValidComponentType($this->request->get('transaction_id'))]
        ];

        if($this->request->has('components')) {
            foreach ($this->request->all('components') as $index => $component) {
                if(!empty($component['type'])){
                    switch ($component['type']){
                        case 'text':
                            $rules['components.'.$index.'.format'] = 'required';
                            break;
                        case 'year':
                            $rules['components.'.$index.'.format'] = 'required|in:y,Y,roman';
                            break;
                        case 'month':
                            $rules['components.'.$index.'.format'] = 'required|in:m,M,F,n,roman';
                            break;
                        case 'day':
                            $rules['components.'.$index.'.format'] = 'required|in:d,D,j,l,roman';
                            break;
                        case 'counter':
                            $rules['components.'.$index.'.format'] = 'required|integer|min:1';
                            break;
                        case 'transaction-branch':
                            $rules['components.'.$index.'.format'] = 'required|in:id,code,name';
                            break;
                        case 'transaction-department':
                            $rules['components.'.$index.'.format'] = 'required|in:id,code,name';
                            break;
                        case 'warning-letter-type':
                            $rules['components.'.$index.'.format'] = 'required|in:id,code,name';
                            break;
                    }
                }

            }
        }

        return $rules;
    }
}
