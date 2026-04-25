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
        $table->id();
        $table->foreignId('barang_id')->constrained();
        $table->unsignedBigInteger('pembeli_id');
        $table->unsignedBigInteger('penjual_id');
        $table->timestamps();
        $table->foreign('pembeli_id')->references('id')->on('users');
        $table->foreign('penjual_id')->references('id')->on('users');
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
