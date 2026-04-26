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
    Schema::create('transaksis', function (Blueprint $table) {
        $table->id('id_transaksi');
        $table->foreignId('id_barang')->constrained('barangs', 'id_barang');
        // id_pembeli dan id_penjual dicatat EKSPLISIT untuk histori
        $table->foreignId('id_pembeli')
              ->constrained('users', 'id_user')->onDelete('cascade');
        $table->foreignId('id_penjual')
              ->constrained('users', 'id_user')->onDelete('cascade');
        $table->decimal('final_price', 15, 2); // Harga yang disepakati di chat
        $table->enum('metode_pembayaran', ['COD', 'Transfer', 'Barter'])
              ->default('COD');
        $table->enum('metode_pengiriman', ['Ambil Sendiri', 'Dikirim'])
              ->default('Ambil Sendiri');
        $table->enum('status_transaksi', ['pending', 'proses', 'selesai', 'batal'])
              ->default('pending');
        $table->timestamp('tanggal_deal')->useCurrent();
        $table->timestamps();
    });

}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
