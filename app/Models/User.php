<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 1. Tetap import ini

class User extends Authenticatable
{
    // 2. WAJIB tambahkan HasApiTokens di sini supaya createToken() jalan!
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_mahasiswa',
        'nomor_wa',
        'alamat',
        'lat',
        'long',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_mahasiswa' => 'boolean', // Supaya otomatis jadi true/false
        ];
    }

    // --- RELASI ---
    public function barangs()
    {
        return $this->hasMany(Barang::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function chatsSebagaiPembeli()
    {
        return $this->hasMany(Chat::class, 'pembeli_id');
    }

    public function chatsSebagaiPenjual()
    {
        return $this->hasMany(Chat::class, 'penjual_id');
    }
}