<?php

namespace Database\Seeders;

use App\Models\Specification;
use Illuminate\Database\Seeder;

class SpecificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

            Specification::create([
                'version' => '1.0.0',
                'url'=> 'http://android.nanovijecnik.nanoco.hr'
            ]);

    }
}
