<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'username' => 'admin',
            'email' => 'admin@segosambel.com',
            'password' => Hash::make('123'),
            'role' => 'admin',
        ]);

        User::factory()->create([
            'name' => 'Karyawan',
            'username' => 'karyawan',
            'email' => 'karyawan@segosambel.com',
            'password' => Hash::make('123'),
            'role' => 'karyawan',
        ]);

        User::factory()->create([
            'name' => 'Owner',
            'username' => 'owner',
            'email' => 'owner@segosambel.com',
            'password' => Hash::make('123'),
            'role' => 'owner',
        ]);

        $this->call(DemoDataSeeder::class);
    }
}
