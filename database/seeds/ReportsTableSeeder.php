<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reports = [
            [
                'name' => 'Jemaat',
                'function' => 'jemaat'
            ],
            [
                'name' => 'Inventory',
                'function' => 'inventory'
            ],
        ];

        foreach ($reports as $key => $value) {
            if(\App\Entities\Report::where('function',$value['function'])->count() < 1){
                \App\Entities\Report::create($value);
            }
        }
    }
}
