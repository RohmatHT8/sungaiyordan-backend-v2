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
            ],[
                'name' => 'Branch',
                'subject' => 'App\Entities\Branch'
            ],[
                'name' => 'Department',
                'subject' => 'App\Entities\Department'
            ],[
                'name' => 'Role',
                'subject' => 'App\Entities\Role'
            ],[
                'name' => 'WebUser',
                'subject' => 'App\Entities\WebUser'
            ],[
                'name' => 'WebFamilyCard',
                'subject' => 'App\Entities\WebFamilyCard'
            ],[
                'name' => 'PermissionSetting',
                'subject' => 'App\Entities\PermissionSetting'
            ],[
                'name' => 'Department',
                'subject' => 'App\Entities\Department'
            ],[
                'name' => 'Shdr',
                'subject' => 'App\Entities\Shdr'
            ],[
                'name' => 'NumberSetting',
                'subject' => 'App\Entities\NumberSetting'
            ],[
                'name' => 'Baptism',
                'subject' => 'App\Entities\Baptism'
            ],[
                'name' => 'ChildSubmission',
                'subject' => 'App\Entities\ChildSubmission'
            ],[
                'name' => 'MarriageCertificate',
                'subject' => 'App\Entities\MarriageCertificate'
            ],[
                'name' => 'ConfirmationOfMarriage',
                'subject' => 'App\Entities\ConfirmationOfMarriage'
            ],[
                'name' => 'FamilyCard',
                'subject' => 'App\Entities\FamilyCard'
            ],[
                'name' => 'CongregationalStatus',
                'subject' => 'App\Entities\CongregationalStatus'
            ],[
                'name' => 'Widget',
                'subject' => 'App\Entities\Widget'
            ],[
                'name' => 'WidgetPermissionSetting',
                'subject' => 'App\Entities\WidgetPermissionSetting'
            ],[
                'name' => 'ReportPermissionSetting',
                'subject' => 'App\Entities\ReportPermissionSetting'
            ],[
                'name' => 'Building',
                'subject' => 'App\Entities\Building'
            ],[
                'name' => 'Room',
                'subject' => 'App\Entities\Room'
            ],[
                'name' => 'ItemType',
                'subject' => 'App\Entities\ItemType'
            ],[
                'name' => 'Item',
                'subject' => 'App\Entities\Item'
            ],
        ];

        foreach ($transactions as $key => $value) {
            if(\App\Entities\Transaction::where('subject',$value['subject'])->count() < 1){
                \App\Entities\Transaction::create($value);
            }
        }

    }
}
