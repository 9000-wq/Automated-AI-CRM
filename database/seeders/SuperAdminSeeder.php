<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Company;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Check if company already exists by email
        $company = Company::firstOrCreate(
            ['company_email' => 'customsoftware2022@gmail.com'],
            [
                'company_name' => 'Custom Software',
                'business_type' => 'IT Company',
                'company_address' => 'Model Town F Block Lahore',
                'country' => 'Pakistan',
            ]
        );

        // Check if user already exists by email
        User::firstOrCreate(
            ['email' => 'customsoftware2022@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('12345678'),
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'company_id' => $company->id,
                'user_role' => 'super admin',
            ]
        );
    }
}
