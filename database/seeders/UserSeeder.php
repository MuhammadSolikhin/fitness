<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Admin Fitness',
            'email' => 'admin@fitness.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_membership' => false,
        ]);

        // Pelatih Utama
        User::create([
            'name' => 'Pelatih Yoga',
            'email' => 'pelatih@fitness.com',
            'password' => Hash::make('password'),
            'role' => 'pelatih',
            'is_membership' => false,
        ]);

        // Tambahan Pelatih
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Pelatih {$i}",
                'email' => "pelatih{$i}@fitness.com",
                'password' => Hash::make('password'),
                'role' => 'pelatih',
                'is_membership' => false,
            ]);
        }

        // Beberapa User Biasa
        for ($i = 1; $i <= 10; $i++) {
            User::create([
                'name' => 'User Biasa ' . $i,
                'email' => "user{$i}@fitness.com",
                'password' => Hash::make('password'),
                'role' => 'user',
                'is_membership' => false,
            ]);
        }
    }
}
