<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TransactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $transactions = [
            [
                'name' => 'User',
                'subject' => 'App\Entities\User'
            ],
            [
                'name' => 'Branch',
                'subject' => 'App\Entities\Branch'
            ],
            [
                'name' => 'Department',
                'subject' => 'App\Entities\Department'
            ],
            [
                'name' => 'WebUser',
                'subject' => 'App\Entities\WebUser'
            ],
        ];

        foreach ($transactions as $key => $value) {
            if(\App\Entities\Transaction::where('subject',$value['subject'])->count() < 1){
                \App\Entities\Transaction::create($value);
            }
        }

    }
}
