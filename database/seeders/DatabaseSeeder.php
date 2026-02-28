<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;   // ⭐ مهم

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'donia.a5ra2019@gmail.com'],
            [
                'name' => 'Mohamed Tarek',
                'email' => 'donia.a5ra2019@gmail.com',
                'password' => Hash::make('123456789'),
            ]
        );

        // Seed roles
        $this->call(RoleSeeder::class);

        // Seed company data
        $this->call(CompanySeeder::class);

        // Seed Udhiya (Sacrifice) data
        $this->call(UdhiyaSeeder::class);
    }
}
