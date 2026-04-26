<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model {
    protected $primaryKey = 'id_barang';

    protected $fillable = [
        'id_user', 'kategori', 'nama_barang', 'deskripsi', 'spesifikasi',
        'harga_normal', 'harga_khusus', 'radius_lokasi', 'latitude', 'longitude',
        'status', 'is_negotiable', 'is_barter', 'keinginan_barter', 'foto_barang',
    ];

    // RELASI: Pemilik/penjual barang ini
    public function penjual() {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    // RELASI: Semua transaksi yang pernah terjadi untuk barang ini
    public function transaksis() {
        return $this->hasMany(Transaksi::class, 'id_barang', 'id_barang');
    }

    // SCOPE: Hanya barang yang tidak disembunyikan sistem
    public function scopeVisible($query) {
        return $query->where('is_hidden', false)->where('status', '!=', 'sold');
    }

    // SCOPE: Filter berdasarkan kategori
    public function scopeKategori($query, $kategori) {
        return $query->where('kategori', $kategori);
    }
}

