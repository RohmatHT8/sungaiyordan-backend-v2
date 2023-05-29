<?php

namespace App\Rules;

use App\Entities\PermissionMapping;
use App\Entities\ReportPermissionMapping;
use Illuminate\Contracts\Validation\Rule;

class ValidRoleBranchPermission implements Rule
{
    protected $attribute;
    protected $otherAttribute;
    protected $otherIds;
    protected $id;
    protected $message;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($attribute,$otherAttribute,$otherIds,$id=null)
    {
        $this->attribute = $attribute;
        $this->otherAttribute = $otherAttribute;
        $this->otherIds = $otherIds;
        $this->id = $id;
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
        if(empty($value) || empty($this->otherIds) || count($this->otherIds) < 1){
            return true;
        }

        $otherIds = PermissionMapping::where($this->attribute,$value)->whereIn($this->otherAttribute,$this->otherIds)
            ->when(!empty($this->id),function ($q){
                $q->where('permission_setting_id','!=',$this->id);
            })->pluck($this->otherAttribute)->all();

        if(count($otherIds)){
            $attribute = explode('_',$this->attribute)[0];
            $attributeEntity = ('App\Entities\\'.ucfirst($attribute))::find($value);

            $otherAttribute = explode('_',$this->otherAttribute)[0];
            $otherAttributeEntity = ('App\Entities\\'.ucfirst($otherAttribute))::find($otherIds[0]);

            $this->message = 'Combination of '.$attribute.': '.$attributeEntity->name.
                ' and '.$otherAttribute.': '.$otherAttributeEntity->name.' is exist.';
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
