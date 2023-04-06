<?php

use Illuminate\Database\Seeder;

class PermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $abilities = ['index','create','read','update','delete'];
        $transactionAbilities = [
            'CompanySetting' => ['index','create'],
            'UserAbsenceLog' => ['index','read','update','delete'],
            'JournalClosing' => ['index','create','read','delete'],
            'Widget' => ['index','read','update'],
            'TestPapi' => ['create','read'],
        ];

        $closeTransactions = ['PurchaseRequest','PurchaseOrder',
            'Journal','SalesOrder','DeliveryOrder','StockOpnameRequest',
            'InternalDeliveryOrder','WorkOrder','ManpowerRequest','TrainingRequest'];

        $verifyTransactions = ['CheckSheetOutgoing', 'CheckSheetIncoming'];

        foreach (\App\Entities\Transaction::all() as $transaction){
            $tempAbilities = $abilities;
            if(in_array($transaction->name,array_keys($transactionAbilities))){
                $tempAbilities = $transactionAbilities[$transaction->name];
            }

            foreach ($tempAbilities as $ability){
                $ability = strtolower($transaction->name).'-'.$ability;
                if(\App\Entities\Permission::where('ability',$ability)->count()<1){
                    \App\Entities\Permission::create([
                        'ability' => $ability,
                        'transaction_id' => $transaction->id
                    ]);
                }
            }

            if(in_array($transaction->name,$closeTransactions)){
                $ability = strtolower($transaction->name).'-close';
                if(\App\Entities\Permission::where('ability',$ability)->count()<1){
                    \App\Entities\Permission::create([
                        'ability' => $ability,
                        'transaction_id' => $transaction->id
                    ]);
                }
            }

            if(in_array($transaction->name,$verifyTransactions)){
                $ability = strtolower($transaction->name).'-verify';
                if(\App\Entities\Permission::where('ability',$ability)->count()<1){
                    \App\Entities\Permission::create([
                        'ability' => $ability,
                        'transaction_id' => $transaction->id
                    ]);
                }
            }
        }
    }
}
