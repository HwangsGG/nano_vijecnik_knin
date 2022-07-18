<?php

namespace Database\Seeders;

use App\Models\CouncilSessionItemType;
use Illuminate\Database\Seeder;

class CouncilSessionItemTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['Kvorum','{"has_voting":true,"mandatory":true,"time_limited":true,"time":15}',1],
            ['Stavka dnevnog reda','{"has_voting":true,"mandatory":true,"time_limited":false}',1],
            ['Vijecnicka pitanja','{"has_voting":false,"mandatory":false,"time_limited":false}',1]
        ];

        foreach ($types as $account) {
            CouncilSessionItemType::create([
                'name' => $account[0],
                'data'=>$account[1],
                'active'=> $account[2]
            ]);
        }
    }
}
