<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WidgetsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $widgets = [
            [
                'name' => 'Quote',
                'function' => 'quote',
                'default' => true,
                'base_sequence' => 1
            ],[
                'name' => 'Barchart',
                'function' => 'barchart',
                'base_sequence' => 2
            ]
        ];

        foreach ($widgets as $index => $value) {
            if(\App\Entities\Widget::where('function',$value['function'])->count() < 1){
                $widget = \App\Entities\Widget::create($value);

                if ($widget->default) {
                    foreach (\App\Entities\User::all() as $user) {
                        \App\Entities\UserWidget::create([
                            'widget_id' => $widget->id,
                            'user_id' => $user->id,
                            'show' => true,
                            'sequence' => $widget->base_sequence
                        ]);
                    }
                } else {
                    foreach (\App\Entities\User::whereHas('roles',function($q){
                        $q->where('role_id',1);
                    })->get()->where('role_id',1) as $admin) {
                        \App\Entities\UserWidget::create([
                            'widget_id' => $widget->id,
                            'user_id' => $admin->id,
                            'show' => true,
                            'sequence' => $widget->base_sequence
                        ]);
                    }
                }
            }
        }
    }
}
