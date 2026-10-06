<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin Kampus',
            'email' => 'admin@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Mahasiswa Contoh',
            'email' => 'mahasiswa@kampus.test',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
        ]);
    }
}
