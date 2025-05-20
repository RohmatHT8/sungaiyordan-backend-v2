<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemSelect extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'no' => $this->no,
            'name' => $this->no.' - '.$this->name,
        ];
    }
}
