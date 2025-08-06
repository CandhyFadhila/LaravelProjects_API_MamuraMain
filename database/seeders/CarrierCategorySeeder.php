<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class CarrierCategorySeeder extends Seeder
{
public function run(): void
    {
        $faker = Faker::create();

        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'name' => ucfirst($faker->unique()->words(2, true)),
                'description' => $faker->sentence(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('carrier_categories')->insert($data);
    }

}
