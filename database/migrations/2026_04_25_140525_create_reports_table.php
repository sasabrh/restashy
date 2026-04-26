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
    Schema::create('ratings', function (Blueprint $table) {
        $table->id('id_rating');
        $table->foreignId('id_transaksi')->constrained('transaksis', 'id_transaksi');
        $table->foreignId('id_penulis')  // User yang memberi rating
              ->constrained('users', 'id_user');
        $table->foreignId('id_target')   // User penjual yang diberi rating
              ->constrained('users', 'id_user');
        $table->tinyInteger('skor');     // Nilai 1-5
        $table->text('komentar')->nullable();
        $table->timestamps();
        // Satu transaksi hanya bisa punya satu rating
        $table->unique('id_transaksi');
    });

}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};
