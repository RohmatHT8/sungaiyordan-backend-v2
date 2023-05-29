<?php

namespace App\Http\Resources;

use App\Entities\Branch;
use Illuminate\Http\Resources\Json\ResourceCollection;

class webUserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'data' =>  $this->collectResource($this->collection)->transform(function($model){
                $branch = Branch::where('id', $model->webUserFamilyCard['branch_id'])->pluck('name');
                return [
                    'id' => $model->id,
                    'nik' => $model->nik,
                    'name' => $model->name,
                    'family_member_status' => $model->family_member_status,
                    'congregational_status' => $model->congregational_status,
                    'no_kk' => $model->webUserFamilyCard['no_kk'],
                    'branch_name' => $branch
                ];
            })
        ];
    }
}
