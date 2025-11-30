<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::updateOrCreate(
            ['email' => 'admin@e-tempah.test'],
            [
                'name' => 'Administrator',
                'email' => 'admin@e-tempah.test',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Staff User
        User::updateOrCreate(
            ['email' => 'staff@e-tempah.test'],
            [
                'name' => 'Staff User',
                'email' => 'staff@e-tempah.test',
                'password' => Hash::make('password'),
                'role' => 'staff',
                'is_verified' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Admin and Staff users seeded successfully!');
        $this->command->table(
            ['Role', 'Email', 'Password'],
            [
                ['Admin', 'admin@e-tempah.test', 'password'],
                ['Staff', 'staff@e-tempah.test', 'password'],
            ]
        );
    }
}
