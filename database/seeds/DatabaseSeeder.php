<?php

use Database\Seeders\ReportPermissionsTableSeeder;
use Database\Seeders\ReportsTableSeeder;
use Database\Seeders\TransactionsTableSeeder;
use Database\Seeders\WidgetPermissionsTableSeeder;
use Database\Seeders\WidgetsTableSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(TransactionsTableSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(WidgetsTableSeeder::class);
        $this->call(WidgetPermissionsTableSeeder::class);
        $this->call(ReportsTableSeeder::class);
        $this->call(ReportPermissionsTableSeeder::class);
    }
}
