<?php

namespace App\Rules;

use App\Entities\NumberSetting;
use App\Entities\Transaction;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Schema;

class ValidTransactionForNumbering implements Rule
{
    protected $id;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($id=null)
    {
        $this->id = $id;
        $this->message = 'Transaction is not valid for automatic numbering.';
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if(empty($value)){
            return true;
        }

        $transaction = Transaction::find($value);
        if(empty($transaction)){
            return true;
        }

        if(NumberSetting::where('transaction_id',$value)->when(!empty($this->id),function ($q){
            $q->where('id','!=',$this->id);
        })->count()){
            $this->message = 'Numbering setting for this transaction is exist.';
            return false;
        }

        return Schema::hasColumn((new $transaction->subject)->getTable(), 'no');
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
