<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            RoleTableSeeder::class,
            PermissionTableSeeder::class,
            CityTableSeeder::class,
            AccountTypeTableSeeder::class,
            AccountTableSeeder::class,
            UserTableSeeder::class,
            CouncilSessionItemTypeTableSeeder::class,
            CouncilSessionTableSeeder::class,
            CouncilSessionItemTableSeeder::class,
            VoteTypeTableSeeder::class,
            SpecificationTableSeeder::class
        ]);
        // \App\Models\User::factory(10)->create();
    }
}
