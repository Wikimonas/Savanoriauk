<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // If no admin exists, create a new one
        if (!$admin) {
            User::create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => Hash::make('adminpassword'), // Use a strong password in production
                'role' => 'admin', // Ensure the role is set to 'admin'
            ]);
        }
    }
}
