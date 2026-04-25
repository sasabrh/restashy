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

    public function completeTransaction($id)
{
    $transaksi = Transaksi::find($id);
    
    if (!$transaksi) return response()->json(['message' => 'Tidak ditemukan'], 404);

    // Update status transaksi
    $transaksi->update(['status' => 'completed']);

    // Update status barang jadi SOLD otomatis
    \App\Models\Barang::where('id', $transaksi->barang_id)->update(['status' => 'sold']);

    return response()->json([
        'success' => true,
        'message' => 'Transaksi selesai, barang telah terjual!',
    ]);
}
}