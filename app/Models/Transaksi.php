<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    protected $guarded = ['id'];

    public function chat() {
        return $this->belongsTo(Chat::class);
    }

    public function rating() {
        return $this->hasOne(Rating::class);
    }
}