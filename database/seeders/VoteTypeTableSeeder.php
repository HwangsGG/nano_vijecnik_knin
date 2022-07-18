<?php

namespace Database\Seeders;

use App\Models\VoteType;
use Illuminate\Database\Seeder;

class VoteTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            ['DA',1],
            ['NE',1],
            ['SUZDRŽAN',1],

        ];

        foreach ($types as $account) {
            VoteType::create([
                'name' => $account[0],
                'active'=>$account[1],
            ]);
        }
    }
}
