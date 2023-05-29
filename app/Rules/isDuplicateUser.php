<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class isDuplicateUser implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function passes($attribute, $value)
    {
        $ids = [];
        foreach($value as $user) {
            array_push($ids,$user['user_id']);
        }
        return !(count($ids) !== count(array_unique($ids)));
    }

    public function message()
    {
        return 'Nama Jemaat Tidak Boleh Duplikat';
    }
}
