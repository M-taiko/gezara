<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Administrator',
                'description' => 'Full system access and administrative privileges',
            ]
        );

        $managerRole = Role::firstOrCreate(
            ['name' => 'manager'],
            [
                'display_name' => 'Manager',
                'description' => 'Can manage users and view reports',
            ]
        );

        $userRole = Role::firstOrCreate(
            ['name' => 'user'],
            [
                'display_name' => 'User',
                'description' => 'Basic user with limited access',
            ]
        );

        // Assign admin role to the test user (if exists)
        $testUser = User::where('email', 'donia.a5ra2019@gmail.com')->first();
        if ($testUser) {
            // Check if role is not already assigned
            if (!$testUser->roles()->where('role_id', $adminRole->id)->exists()) {
                $testUser->roles()->attach($adminRole);
            }
        }
    }
}
