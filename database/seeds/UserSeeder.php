<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        App\Entities\User::create([
            'name' => 'Rohmat Hidayattullah',
            'email' => 'rohmathidayattullah@gmail.com',
            'password' => bcrypt('hacktiv123'),
            'nik' => '111111',
            'no_ktp' => '030303303',
            'place_of_birth' => 'Sukabumi',
            'date_of_birth' => '1997-10-19',
            'gender' => 'Male',
            'ktp_address' => 'Sukabumi',
            'address' => 'Jakarta',
            'pos_code' => '109238',
            'phone_number' => '085772428162',
            'father' => 'Rasep Sukarnajaya',
            'mother' => 'Kokom Komalasari',
        ]);
    }
}
