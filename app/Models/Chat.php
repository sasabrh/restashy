<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $guarded = ['id'];

    public function barang() {
        return $this->belongsTo(Barang::class);
    }

    public function pembeli() {
        return $this->belongsTo(User::class, 'pembeli_id');
    }

    public function penjual() {
        return $this->belongsTo(User::class, 'penjual_id');
    }

    public function pesans() {
        return $this->hasMany(Pesan::class);
    }

    public function transaksi() {
        return $this->hasOne(Transaksi::class);
    }
}
