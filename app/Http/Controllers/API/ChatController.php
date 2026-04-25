<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Kirim Pesan Baru
    public function sendMessage(Request $request)
    {
        $chat = Chat::create([
            'barang_id'   => $request->barang_id,
            'sender_id'   => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'pesan'       => $request->pesan,
        ]);

        return response()->json(['success' => true, 'data' => $chat]);
    }

    // Ambil Riwayat Chat per Barang
    public function getChatHistory($barang_id)
    {
        $chats = Chat::where('barang_id', $barang_id)
            ->where(function($q) {
                $q->where('sender_id', auth()->id())
                  ->orWhere('receiver_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $chats]);
    }

    public function deal(Request $request)
{
    // 1. Validasi input
    $request->validate([
        'barang_id' => 'required|exists:barangs,id',
        'pembeli_id' => 'required|exists:users,id',
        'final_price' => 'required|integer',
    ]);

    // 2. Buat Transaksi Baru dengan status pending
    $transaksi = \App\Models\Transaksi::create([
        'barang_id' => $request->barang_id,
        'pembeli_id' => $request->pembeli_id,
        'penjual_id' => auth()->id(),
        'final_price' => $request->final_price,
        'status' => 'pending', // Menunggu pembayaran/konfirmasi
    ]);

    // 3. Kirim pesan otomatis ke chat sebagai notifikasi Deal
    Chat::create([
        'barang_id' => $request->barang_id,
        'sender_id' => auth()->id(),
        'receiver_id' => $request->pembeli_id,
        'pesan' => "DEAL! Penjual menyepakati harga Rp " . number_format($request->final_price, 0, ',', '.'),
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Kesepakatan tercapai! Transaksi dibuat.',
        'data' => $transaksi
    ]);
}
}
