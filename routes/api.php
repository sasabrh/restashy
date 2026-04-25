<?php

use App\Http\Controllers\API\BarangController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\WishlistController;
use App\Http\Controllers\API\TransaksiController; // Jangan lupa import ini nanti
use Illuminate\Support\Facades\Route;

/* --- Public Routes --- */
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Orang bisa lihat daftar barang & detail barang tanpa login
Route::get('/barang', [BarangController::class, 'index']);
Route::get('/barang/{id}', [BarangController::class, 'show']);
Route::get('/kategori', function() {
    return response()->json([
        'success' => true,
        'data' => ['akademik', 'perabot', 'pakaian', 'gratis', 'lainnya']
    ]);
});

/* --- Protected Routes (Wajib Login) --- */
Route::middleware('auth:sanctum')->group(function () {
    // Auth & User
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Barang (Hanya untuk Store, Update, Delete)
    Route::post('/barang', [BarangController::class, 'store']);
    Route::put('/barang/{id}', [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
    Route::get('/my-barang', [BarangController::class, 'userBarang']); // Barang milik saya

    // Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist', [WishlistController::class, 'store']);

    // Chat & Nego
    Route::post('/chat', [ChatController::class, 'sendMessage']);
    Route::get('/chat/{barang_id}', [ChatController::class, 'getChatHistory']);
    Route::post('/chat/deal', [ChatController::class, 'deal']); // Route untuk tombol DEAL

    // Transaksi
    Route::post('/transaksi/{id}/complete', [TransaksiController::class, 'completeTransaction']);
});