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
    Schema::create('chats', function (Blueprint $table) {
        $table->id('id_pesan');
        $table->foreignId('id_barang')->constrained('barangs', 'id_barang')
              ->onDelete('cascade');
        $table->foreignId('id_pengirim')
              ->constrained('users', 'id_user')->onDelete('cascade');
        $table->foreignId('id_penerima')
              ->constrained('users', 'id_user')->onDelete('cascade');
        $table->text('isi_pesan');
        // Tipe pesan: text biasa, atau 'deal' saat tombol Deal diklik
        $table->enum('tipe', ['text', 'deal', 'system'])->default('text');
        $table->boolean('sudah_dibaca')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
