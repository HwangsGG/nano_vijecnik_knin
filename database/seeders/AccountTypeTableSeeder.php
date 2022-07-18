<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            ['Gradsko vijeće','	{"custom_data_field":"custom_value"}',1],
            ['Županijsko vijeće','	{"custom_data_field":"custom_value"}',1]
        ];

        foreach ($accounts as $account) {
            AccountType::create([
                'name' => $account[0],
                'data'=>$account[1],
                'active'=> $account[2]
            ]);
        }
    }
}
