<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $cities = [
            ['Šibenik',22000],
            ['Knin',22300]
        ];

        foreach ($cities as $city) {
            City::create([
                'name' => $city[0],
                'post_code'=> $city[1]
            ]);
        }
    }
}
