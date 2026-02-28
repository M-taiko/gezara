<?php

namespace Database\Seeders;

use App\Models\Company;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Company::firstOrCreate(
            ['name' => 'Valex Company'],
            [
                'name' => 'Valex Company',
                'email' => 'info@valex.com',
                'phone' => '+20 100 123 4567',
                'address' => 'Cairo, Egypt',
                'website' => 'https://valex.com',
                'description' => 'Valex - Premium Admin Dashboard Template. Built with Bootstrap and advanced components.',
                'logo' => 'assets/img/brand/logo.png',
                'sidebar_logo_expanded' => 'assets/img/brand/logo.png',
                'sidebar_logo_collapsed' => 'assets/img/brand/favicon.png',
            ]
        );
    }
}
