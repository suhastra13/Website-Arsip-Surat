<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::firstOrCreate(
            ['email' => 'admin@arsip.test'],
            [
                'name'     => 'Admin Arsip',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Staf
        User::firstOrCreate(
            ['email' => 'staf@arsip.test'],
            [
                'name'     => 'Staf Arsip',
                'password' => Hash::make('password123'),
                'role'     => 'staf',
            ]
        );
    }
}
