<?php

namespace Database\Seeders\CoverageArea;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class SupportedCitySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $data = [];

        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'name' => 'Kota ' . $faker->unique()->city,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('supported_cities')->insert($data);
    }
}
