<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WidgetPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Entities\Widget::all() as $widget){
            if(\App\Entities\WidgetPermission::where('widget_id',$widget->id)->count()<1){
                \App\Entities\WidgetPermission::create([
                    'ability' => 'widget-'.strtolower($widget->name),
                    'widget_id' => $widget->id
                ]);
            }
        }
    }
}
