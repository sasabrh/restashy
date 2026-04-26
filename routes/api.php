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

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);
Route::get('/barang',    [BarangController::class, 'index']);  // Browse semua
Route::get('/barang/{id}', [BarangController::class, 'show']); // Detail barang

// ── PROTECTED ROUTES (butuh login / token) ─────
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);
    Route::put('/me',      [AuthController::class, 'updateProfile']);

    // Barang (CRUD — hanya pemilik yang bisa edit/hapus)
    Route::post('/barang',        [BarangController::class, 'store']);
    Route::put('/barang/{id}',    [BarangController::class, 'update']);
    Route::delete('/barang/{id}', [BarangController::class, 'destroy']);
    Route::get('/my-barang',      [BarangController::class, 'myBarang']);

    // Chat & Deal
    Route::get('/chat/{id_barang}',    [ChatController::class, 'getChat']);
    Route::post('/chat',               [ChatController::class, 'kirimPesan']);
    Route::post('/chat/deal',          [ChatController::class, 'buatDeal']);
    Route::get('/chat/list',           [ChatController::class, 'daftarChat']);

    // Transaksi
    Route::get('/transaksi',           [TransaksiController::class, 'index']);
    Route::put('/transaksi/{id}',      [TransaksiController::class, 'updateStatus']);

    // Rating
    Route::post('/rating',             [RatingController::class, 'store']);

    // Wishlist
    Route::get('/wishlist',            [WishlistController::class, 'index']);
    Route::post('/wishlist',           [WishlistController::class, 'store']);
    Route::delete('/wishlist/{id}',    [WishlistController::class, 'destroy']);

    // Report
    Route::post('/report',             [ReportController::class, 'store']);
});
