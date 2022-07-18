<?php

namespace Database\Seeders;

use App\Models\CouncilSessionItem;
use Illuminate\Database\Seeder;

class CouncilSessionItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = [
            [1,1,'Kvorum',1,1,0],
            [1,2,'Usvajanje zapisnika s 6. sjednice GV-a',1,1,0],
            [24,2,'Usvajanje dnevnog reda',1,1,0],
            [15,2,'1. Prijedlog Odluke o davanju suglasnosti za nabavu ložulja za potrebe DV Cvrcak Knin za 2022. godinu',1,1,1],
            [3,2,'2. Prijedlog za imenovanje vršitelja dužnosti ravnatelja Kninskog muzeja Knin',1,1,2],
            [6,3,'3. Vijecnicka pitanja',1,1,3],
            [4,1,'2. Kvorum',1,1,0],
            [4,2,'7. Usvajanje zapisnika s 6. sjednice GV-a',1,1,0],
            [5,2,'8. Usvajanje dnevnog reda',1,1,0],
            [6,2,'11. Prijedlog Odluke o davanju suglasnosti za nabavu ložulja za potrebe DV Cvrcak Knin za 2022. godinu',1,1,1],
            [7,2,'22. Prijedlog za imenovanje vršitelja dužnosti ravnatelja Kninskog muzeja Knin',1,1,2],
            [8,3,'33. Vijecnicka pitanja',1,1,3],

        ];

        foreach ($types as $account) {
            CouncilSessionItem::create([
                'council_session_id' => $account[0],
                'council_session_item_type_id'=>$account[1],
                'name'=> $account[2],
                'locked'=> $account[3],
                'active'=> $account[4],
                'item_number'=> $account[5],
            ]);
        }
    }
}
