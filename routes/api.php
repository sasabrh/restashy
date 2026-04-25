<?php

use App\Http\Controllers\API\BarangController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

// Barang Resource (Index, Store, Show, Update, Destroy)
Route::apiResource('barang', BarangController::class);

// Daftar barang milik user tertentu
Route::get('user/{id}/barang', [BarangController::class, 'userBarang']);

// Daftar kategori (bisa kirim statis saja dulu)
Route::get('kategori', function() {
    return response()->json([
        'success' => true,
        'data' => ['akademik', 'perabot', 'pakaian', 'gratis', 'lainnya']
    ]);
});

// Public Routes (Bisa diakses tanpa login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes (Harus bawa Token / Login dulu)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
});