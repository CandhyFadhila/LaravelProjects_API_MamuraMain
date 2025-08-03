<?php

namespace Database\Seeders\Auth;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dates = Carbon::now();
        $super_admin_account = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('superadmin123'),
            'account_status' => 2,
            'register_at' => $dates,
            'created_at' => $dates,
            'updated_at' => $dates
        ]);

        $super_admin_account->assignRole('Super Admin');

        $userAccount = User::create([
            'name' => 'Users',
            'email' => 'user@gmail.com',
            'password' => Hash::make('user123'),
            'account_status' => 2,
            'register_at' => $dates,
            'created_at' => $dates,
            'updated_at' => $dates
        ]);

        $userAccount->assignRole('Users');
    }
}
