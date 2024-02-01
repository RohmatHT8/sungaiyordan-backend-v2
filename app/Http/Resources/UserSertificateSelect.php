<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class UserSertificateSelect extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        Log::info(json_decode(json_encode($this->get()),true));
        return [
            'id' => $this->id,
            'name' => $this->nik.' - '.$this->name,
            'address' => $this->address,
            'father' => $this->father,
            'mother' => $this->mother,  
        ];
    }
}
