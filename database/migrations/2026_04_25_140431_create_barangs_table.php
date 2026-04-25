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
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->string('nama_barang');
        $table->text('deskripsi');
        $table->integer('harga_umum');
        $table->integer('harga_mahasiswa'); 
        $table->enum('status', ['available', 'sold'])->default('available');
        $table->boolean('is_negotiable')->default(true);
        $table->enum('kategori', ['akademik', 'perabot', 'pakaian', 'gratis', 'lainnya']);
        $table->string('foto')->nullable();
        $table->text('alamat_jemput')->nullable(); // Alamat spesifik lokasi barang
        $table->decimal('lat', 10, 8)->nullable(); 
        $table->decimal('long', 11, 8)->nullable();
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
