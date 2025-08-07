<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\Auth\AccountSeeder;
use Database\Seeders\Auth\PermissionSeeder;
use Database\Seeders\Auth\RoleSeeder;
use Database\Seeders\Blog\BlogCategorySeeder;
use Database\Seeders\Carrier\CarrierSeeder;
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

            CarrierCategorySeeder::class,
            EmployeeStatusSeeder::class,
            JobLocationSeeder::class,
            CarrierSeeder::class,
            ContentSeeder::class
        ]);
    }
}
