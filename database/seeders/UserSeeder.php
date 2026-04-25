<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    \App\Models\User::create([
        'name' => 'Sasa Pembeli',
        'email' => 'sasa@example.com',
        'password' => bcrypt('password'),
        'is_mahasiswa' => true,
        'nomor_wa' => '08123456789',
        'alamat' => 'Dekat Kampus ITB',
        'lat' => -6.8915,
        'long' => 107.6107,
    ]);

    \App\Models\User::create([
        'name' => 'Budi Penjual',
        'email' => 'budi@example.com',
        'password' => bcrypt('password'),
        'is_mahasiswa' => false,
        'nomor_wa' => '08987654321',
        'alamat' => 'Dipatiukur',
        'lat' => -6.8933,
        'long' => 107.6186,
    ]);
}
}
