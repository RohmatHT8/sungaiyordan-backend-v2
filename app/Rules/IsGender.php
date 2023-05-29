<?php

namespace App\Rules;

use App\Entities\User;
use Illuminate\Contracts\Validation\Rule;

class IsGender implements Rule
{
    private $gender;
    private $message;
    public function __construct($gender)
    {   
        $this->gender = $gender;
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
        $isWomen = User::where('id',$value)->pluck('gender')->first();
        $groomBride = $attribute == 'groom' ? 'Suami' : 'Istri';
        if($isWomen === $this->gender) {
            return true;
        }
        $this->message = 'Jenis Kelamin '.$groomBride.' harus '.$this->gender;

        return false;
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
