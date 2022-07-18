<?php

namespace Database\Seeders;

use App\Models\CouncilSession;
use Illuminate\Database\Seeder;

class CouncilSessionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sessions = [
            ['7. Sjednica GV-a 2024. godine','{"mandatory_quorum":true,"documents":["1","2","3"],"votes":["1","2","3"],"is_finished":false,"breaks":["1","2","3"]}'],
        ];

        for ($i=1;$i<=100;$i++){
            CouncilSession::create([
                'name' =>$i. '. Sjednica GV-a 2024. godine',
                'data'=>'{"mandatory_quorum":true,"documents":["1","2","3"],"votes":["1","2","3"],"is_finished":false,"breaks":["1","2","3"]}',
                'date'=>'2024-12-04',
                'start_time'=>'2024-12-04 11:00:00',
                'end_time'=>'2024-12-04 12:00:00',
                'locked'=>1,
                'active'=> 1
            ]);

        }

        foreach ($sessions as $session) {
            CouncilSession::create([
                'name' => $session[0],
                'data'=>$session[1],
                'date'=>'2024-12-04',
                'start_time'=>'2024-12-04 11:00:00',
                'end_time'=>'2024-12-04 12:00:00',
                'locked'=>1,
                'active'=> 1
            ]);
        }
    }
}
