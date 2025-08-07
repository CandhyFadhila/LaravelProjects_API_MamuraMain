<?php

namespace Database\Seeders\Pricing;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PricingCategorySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Home',
                'description' => 'Kategori harga untuk kebutuhan rumah tangga.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ],
            [
                'name' => 'Business',
                'description' => 'Kategori harga untuk kebutuhan bisnis atau komersial.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'deleted_at' => null
            ]
        ];

        DB::table('pricing_categories')->insert($data);
    }
}
