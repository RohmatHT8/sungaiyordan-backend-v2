<?php

namespace App\Rules;

use App\Entities\Transaction;
use Illuminate\Contracts\Validation\Rule;

class ValidComponentType implements Rule
{
    protected $transactionId;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($transactionId)
    {
        $this->transactionId = $transactionId;
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

        $transaction = Transaction::find($this->transactionId);
        if($value == 'warning-letter-type' && $transaction->name != 'UserWarningLetter'){
            $this->message = 'Component type can\'t be applied to selected transaction.';
            return false;
        }

        return true;
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
