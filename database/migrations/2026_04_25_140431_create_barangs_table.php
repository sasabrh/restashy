<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up(): void
{
    Schema::create('barangs', function (Blueprint $table) {
        $table->id('id_barang');
        $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
        // PENTING: kategori disimpan sebagai ENUM, bukan tabel terpisah
        // Ini membuat dropdown di frontend lebih sederhana
        $table->enum('kategori', [
            'Academic Stash',
            'Dorm Living',
            'Wardrobe Finds',
            'Free Items',
            'Lainnya'
        ]);
        $table->string('nama_barang');
        $table->text('deskripsi');
        $table->text('spesifikasi')->nullable(); // Wajib untuk Dorm Living
        $table->decimal('harga_normal', 15, 2);
        $table->decimal('harga_khusus', 15, 2)->nullable(); // Harga mahasiswa
        $table->string('radius_lokasi')->nullable();  // Nama area (teks)
        $table->decimal('latitude', 10, 8)->nullable();
        $table->decimal('longitude', 11, 8)->nullable();
        // Status dengan 4 state: available, negotiating, deal_made, sold
        $table->enum('status', ['available', 'negotiating', 'deal_made', 'sold'])
              ->default('available');
        $table->boolean('is_negotiable')->default(true);
        $table->boolean('is_barter')->default(false);
        $table->string('keinginan_barter')->nullable(); // Ditukar dengan apa
        $table->string('foto_barang')->nullable(); // Path foto utama
        $table->integer('jumlah_laporan')->default(0); // Counter report barang
        $table->boolean('is_hidden')->default(false); // Disembunyikan oleh sistem
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barangs');
    }
};
