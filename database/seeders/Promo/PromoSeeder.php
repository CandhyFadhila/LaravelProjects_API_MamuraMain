<?php

namespace Database\Seeders\Promo;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PromoSeeder extends Seeder
{
public function run(): void
    {
        $nowWib = Carbon::now('Asia/Jakarta');
        $rows = [];

        for ($i = 1; $i <= 20; $i++) {
            $rows[] = [
                'name'            => "Promo Internet Hemat {$i}",
                'description'     => "Promo pemasangan & langganan internet paket hemat {$i}.",
                'terms'           => json_encode([
                    "Promo berlaku untuk pelanggan baru.",
                    "Minimal berlangganan 3 bulan.",
                    "Gratis instalasi setelah aktivasi berhasil.",
                    "Tidak dapat digabung dengan promo lain.",
                    "Berlaku di area cakupan jaringan.",
                ]),
                'promo_value'     => rand(10, 50),
                'promo_end'       => $nowWib->copy()->addDays(rand(7, 180)),
                'created_at'      => $nowWib,
                'updated_at'      => $nowWib,
            ];
        }

        DB::table('promos')->insert($rows);
    }
}
