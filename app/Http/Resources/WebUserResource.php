<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WebUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'family_member_status' => $this->family_member_status,
            'name' => $this->name,
            'father' => $this->father,
            'mother' => $this->mother,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'nik' => $this->nik,
            'date_of_birth' => $this->date_of_birth,
            'place_of_birth' => $this->place_of_birth,
            'gender' => $this->gender,
            'join_date' => $this->join_date,
            'congregational_status' => $this->congregational_status,
            'status_baptize' => $this->status_baptize,
            'date_of_baptize' => $this->date_of_baptize,
            'place_of_baptize' => $this->place_of_baptize,
            'who_baptizes' => $this->who_baptize,
            'status_shdr' => $this->status_shdr,
            'date_shdr' => $this->date_shdr,
            'place_of_shdr' => $this->place_of_shdr,
            'profession' => $this->profession,
            'ktp_address' => $this->ktp_address,
            'marital_status' => $this->martial_status,
            'wedding_date' => $this->wedding_date,
            'place_of_wedding' => $this->place_of_wedding,
            'married_church' => $this->married_church,
            'who_married' => $this->who_married,
        ];
    }
}
