<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ReportPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach (\App\Entities\Report::all() as $report){
            if(\App\Entities\ReportPermission::where('report_id',$report->id)->count()<1){
                \App\Entities\ReportPermission::create([
                    'ability' => 'report-'.strtolower($report->name),
                    'report_id' => $report->id
                ]);
            }
        }
    }
}
