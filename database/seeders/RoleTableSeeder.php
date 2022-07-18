<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin',
            'Predsjednik vijeca',
            'Zamjenik predsjednika vijeca',
            'Predsjednik kluba',
            'Zamjenik predsjednika kluba',
            'Vijecnik',
            'Web korisnik',
            'Gost'

        ];

        foreach ($roles as $role) {
            Role::create([
                'name' => $role,
                'data'=> '{"number_of_questions":2,"question_time":180,"replication_time":180,"violation_time":180,"subject_speech_time":600,"break_request":true}',
                'active'=> 1
            ]);
        }
    }
}
