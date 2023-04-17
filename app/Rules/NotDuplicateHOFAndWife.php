<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class NotDuplicateHOFAndWife implements Rule
{
    protected $family_member_statuses;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($family_member_statuses)
    {
        $this->family_member_statuses = $family_member_statuses;
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
        $countHof = 0;
        $countWife = 0;
        foreach($this->family_member_statuses as $fms){
            if($fms == 'Kepala Keluarga'){
                $countHof++;
            }else if($fms == 'Istri'){
                $countWife++;
            }
        }
        return $countHof <= 1 && $countWife <= 1;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Kepala Keluarga Atau Istri hanya boleh satu dalam 1 Anggota Keluarga';
    }
}
