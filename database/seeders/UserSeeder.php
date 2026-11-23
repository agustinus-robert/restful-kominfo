<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'email' => 'admin@test.com',
                'password_hash' => Hash::make('password123'),
                'role' => 'ADMIN',
            ],
            [
                'name' => 'User Satu',
                'email' => 'user1@test.com',
                'password_hash' => Hash::make('password123'),
                'role' => 'USER',
            ],
            [
                'name' => 'User Dua',
                'email' => 'user2@test.com',
                'password_hash' => Hash::make('password123'),
                'role' => 'USER',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            echo "User dibuat: {$user->name} ({$user->email}) - {$user->role}\n";
        }
    }
}