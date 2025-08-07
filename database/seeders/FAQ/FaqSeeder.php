<?php

namespace Database\Seeders\FAQ;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        $data = [];

        for ($i = 0; $i < 20; $i++) {
            $data[] = [
                'question' => rtrim($faker->sentence(rand(5, 8)), '.') . '?',
                'answer' => $faker->paragraph(rand(1, 2)),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('faqs')->insert($data);
    }
}
