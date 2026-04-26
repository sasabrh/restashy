<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable {
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama', 'email', 'password', 'kontak', 'alamat',
        'latitude', 'longitude', 'is_student', 'foto_profil',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_student' => 'boolean',
        'is_banned'  => 'boolean',
    ];

    // RELASI: Barang yang DIJUAL user ini
    public function barangs() {
        return $this->hasMany(Barang::class, 'id_user', 'id_user');
    }

    // RELASI: Transaksi di mana user ini sebagai PEMBELI
    public function pembelian() {
        return $this->hasMany(Transaksi::class, 'id_pembeli', 'id_user');
    }

    // RELASI: Transaksi di mana user ini sebagai PENJUAL
    public function penjualan() {
        return $this->hasMany(Transaksi::class, 'id_penjual', 'id_user');
    }

    // RELASI: Chat yang DIKIRIM user ini
    public function pesanTerkirim() {
        return $this->hasMany(Chat::class, 'id_pengirim', 'id_user');
    }

    // RELASI: Wishlist milik user ini
    public function wishlists() {
        return $this->hasMany(Wishlist::class, 'id_user', 'id_user');
    }

    // RELASI: Rating yang DITERIMA user ini (sebagai penjual)
    public function ratingsDidapat() {
        return $this->hasMany(Rating::class, 'id_target', 'id_user');
    }
}
