<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Log;

class ValidNumberSettingComponent implements Rule
{
    protected $resetType;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($resetType)
    {
        $this->resetType = $resetType;
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
        if(empty($value) || count($value) < 1){
            return true;
        }

        $componentArray = [];
        foreach($value as $component){
            if(empty($component['type'])){
                return true;
            }

            if($component['type'] == 'counter' && in_array('counter',$componentArray)){
                $this->message = 'Component: counter can\'t be multiple.';
                return false;
            }
            array_push($componentArray,$component['type']);
        }

        $missingComponents = [];

        if(!in_array('counter',$componentArray)){
            array_push($missingComponents,'counter');
        }

        if(!empty($this->resetType)){
            if(!in_array('year',$componentArray) && ($this->resetType == 'yearly' || $this->resetType == 'monthly' ||
                    $this->resetType == 'daily')){
                array_push($missingComponents,'year');
            }

            if(!in_array('month',$componentArray) && ($this->resetType == 'monthly' || $this->resetType == 'daily')){
                array_push($missingComponents,'month');
            }

            if(!in_array('day',$componentArray) && $this->resetType == 'daily'){
                array_push($missingComponents,'day');
            }
        }

        if(count($missingComponents) > 0){
            $this->message = 'Component: '.implode(', ',$missingComponents).' must exist.';
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
