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
            // Opsional tapi disarankan: bersihkan data lama agar hasil akhir tepat 3 data/kategori
            DB::table('pricings')->where('pricing_category_id', $categoryId)->delete();

            // Jumlah paket per kategori
            $totalPackages = 4;

            // Pilih salah satu index sebagai paket rekomendasi
            $recommendedIndex = $faker->numberBetween(0, $totalPackages - 1);

            // (Opsional) nama level paket agar konsisten
            $planNames = ['Basic', 'Plus', 'Pro', 'Max', 'Ultra', 'Prime', 'Giga'];

            for ($i = 0; $i < $totalPackages; $i++) {
                DB::table('pricings')->insert([
                    'pricing_category_id' => $categoryId,
                    'name'               => 'Paket ' . strtoupper($faker->lexify('???')) . ' ' . $planNames[$i],
                    'internet_speed'     => $faker->randomElement([10, 20, 50, 75, 100, 150, 200, 300, 500]) * 1000000, // Mbps -> bps
                    'price'              => $faker->numberBetween(150000, 1500000),
                    'is_recommended'     => $i === $recommendedIndex,
                    'description'        => $faker->sentence(12),
                    'created_at'         => Carbon::now(),
                    'updated_at'         => Carbon::now(),
                    'deleted_at'         => null,
                ]);
            }
        }
    }
}
