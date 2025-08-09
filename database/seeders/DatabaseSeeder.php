<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Auth\AccountSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Blog\BlogCategorySeeder;
use Database\Seeders\Blog\BlogSeeder;
use Database\Seeders\Carrier\CarrierSeeder;
use Database\Seeders\CoverageArea\SupportedCitySeeder;
use Database\Seeders\CoverageArea\SupportedProvinceSeeder;
use Database\Seeders\FAQ\FaqSeeder;
use Database\Seeders\Pricing\PricingCategorySeeder;
use Database\Seeders\Pricing\PricingSeeder;
use Database\Seeders\Promo\PromoSeeder;
use Database\Seeders\Static\ContentTypeSeeder;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            RoleSeeder::class,
            AccountSeeder::class,

            ContentTypeSeeder::class,
            BlogCategorySeeder::class,
            SupportedCitySeeder::class,
            SupportedProvinceSeeder::class,
            PricingCategorySeeder::class,

            CarrierCategorySeeder::class,
            EmployeeStatusSeeder::class,
            JobLocationSeeder::class,
            CarrierSeeder::class,
            ContentSeeder::class,
            PricingSeeder::class,
            FaqSeeder::class,
            BlogSeeder::class,
            PromoSeeder::class
        ]);
    }
}
