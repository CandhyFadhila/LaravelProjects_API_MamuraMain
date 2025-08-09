<?php

namespace Database\Seeders\Pricing;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PricingSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua kategori harga yang sudah ada
        $pricingCategoryIds = DB::table('pricing_categories')->pluck('id')->toArray();

        foreach ($pricingCategoryIds as $categoryId) {
            // Tentukan secara acak index dari 5 paket yang akan menjadi 'recommended'
            $recommendedIndex = rand(0, 4);

            for ($i = 0; $i < 20; $i++) {
                DB::table('pricings')->insert([
                    'pricing_category_id' => $categoryId,
                    'name' => 'Paket ' . strtoupper($faker->unique()->lexify('??')) . ' ' . $faker->randomElement(['Basic', 'Plus', 'Pro', 'Max', 'Ultra']),
                    'internet_speed' => $faker->randomElement([10, 20, 50, 75, 100, 150, 200, 300, 500]) * 1000000, // Mbps to bps
                    'price' => $faker->numberBetween(150000, 1500000),
                    'is_recommended' => $i === $recommendedIndex,
                    'description' => $faker->sentence(12),
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                    'deleted_at' => null
                ]);
            }
        }
    }
}
