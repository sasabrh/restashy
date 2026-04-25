<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    protected $guarded = ['id']; // Membolehkan semua kolom diisi kecuali ID

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function wishlists() {
        return $this->hasMany(Wishlist::class);
    }

    public function chats() {
        return $this->hasMany(Chat::class);
    }
}
