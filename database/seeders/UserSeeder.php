<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password_hash' => Hash::make('password'),
            'role' => 'ADMIN'
        ]);

        echo "Admin dibuat: {$admin->name} ({$admin->email})\n";

        $users = User::factory(10)->create([
            'password_hash' => Hash::make('password'),
            'role' => 'USER'
        ]);

        foreach ($users as $user) {
            echo "Pengguna Biasa dibuat: {$user->name} ({$user->email})\n";
        }
    }
}
