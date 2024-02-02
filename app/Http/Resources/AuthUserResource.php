<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AuthUserResource extends JsonResource
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
            'name' => $this->name,
            'nik' => $this->nik,
            'permissions' => $this->permissions,
            'widget_permissions' => $this->widgetPermissions,
            'widgets' => UserWidgetResource::collection($this->widgets()->with('widget')->get()),
            // 'widgets' => [
            //     0 => [
            //         'id' => 1,
            //         'sequence' => 1,
            //         'show' => 1,
            //         'widget' => [
            //             'base_sequence' => 1,
            //             'default' => 1,
            //             'id' => 1,
            //             'name'=>'Quote'
            //         ]
            //     ],
            //     1 => [
            //         'id' => 2,
            //         'sequence' => 2,
            //         'show' => 1,
            //         'widget'=>[
            //             'base_sequence' => 2,
            //             'default' => 1,
            //             'id' => 2,
            //             'name'=>'Barchart'
            //         ]
            //     ],
                
            // ],
            // 'widget_permissions' => [
            //     // 0 => "widget-birthday",
            //     0 => "widget-quote",
            //     1 => "widget-barchart"
            // ],

        ];
    }
}
