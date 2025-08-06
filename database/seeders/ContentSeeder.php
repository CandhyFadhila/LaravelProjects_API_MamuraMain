<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $data = [];

        for ($i = 0; $i < 200; $i++) {
            $data[] = [
                'content_type_id' => 1,
                'content_file_id' => null,
                'content' => $faker->paragraphs(rand(1, 3), true), // isi konten text random 1–3 paragraf
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ];
        }

        DB::table('contents')->insert($data);
    }
}
