<?php

namespace Database\Seeders\Carrier;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Faker\Factory as Faker;

class CarrierSeeder extends Seeder
{
public function run(): void
    {
        $faker = Faker::create();

        // Ambil semua ID yang tersedia untuk foreign keys
        $carrierCategoryIds = DB::table('carrier_categories')->pluck('id')->toArray();
        $employeeStatusIds = DB::table('employee_statuses')->pluck('id')->toArray();
        $jobLocationIds = DB::table('job_locations')->pluck('id')->toArray();

        $data = [];

        for ($i = 0; $i < 50; $i++) {
            $data[] = [
                'carrier_category_id' => $faker->randomElement($carrierCategoryIds),
                'employee_status_id' => $faker->randomElement($employeeStatusIds),
                'job_location_id' => $faker->randomElement($jobLocationIds),
                'qualification' => json_encode($faker->randomElements([
                    'Sarjana S1',
                    'Pengalaman minimal 2 tahun',
                    'Mampu bekerja di bawah tekanan',
                    'Bahasa Inggris aktif',
                    'Memiliki SIM A',
                    'Terbiasa dengan Microsoft Office',
                    'Komunikatif dan disiplin',
                    'Bersedia ditempatkan di luar kota',
                ], rand(2, 5))),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        DB::table('carriers')->insert($data);
    }
}
