<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Seeder;

class AccountTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $accounts = [
            ['Gradsko vijeće Knin','{"has_fixed_sitting":true}',2,1,1],
            ['Županijsko vijeće Šibenik','{"has_fixed_sitting":false}',1,2,1]
        ];

        foreach ($accounts as $account) {
            Account::create([
                'name' => $account[0],
                'data'=>$account[1],
                'city_id'=>$account[2],
                'account_type_id'=>$account[3],
                'active'=> $account[4]
            ]);
        }
    }
}
