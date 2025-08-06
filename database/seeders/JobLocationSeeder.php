<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class JobLocationSeeder extends Seeder
{
public function run(): void
    {
        $faker = Faker::create();

        $data = [];

        for ($i = 1; $i <= 20; $i++) {
            $data[] = [
                'name' => ucfirst($faker->unique()->words(2, true)), // Contoh: "Mobile Logistics"
                'description' => $faker->sentence(10),               // Contoh: "Kategori ini fokus pada logistik kendaraan modern."
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('job_locations')->insert($data);
    }

}
