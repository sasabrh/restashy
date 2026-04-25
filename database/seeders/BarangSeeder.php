<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BarangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
public function run(): void
{
    \App\Models\Barang::create([
        'user_id' => 2,
        'nama_barang' => 'Kursi Belajar Bekas',
        'deskripsi' => 'Masih bagus, cuma lecet dikit di kaki.',
        'harga_umum' => 150000,
        'harga_mahasiswa' => 120000,
        'kategori' => 'perabot',
        'status' => 'available',
        'is_negotiable' => true,
        'alamat_jemput' => 'Kost Putra Budi, Dago',
        'lat' => -6.8933,
        'long' => 107.6186,
    ]);
}
}
