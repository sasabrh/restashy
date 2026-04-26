<?php

namespace App\Http\Controllers\API;

use App\Models\Chat;
use App\Models\Barang;
use App\Models\Transaksi;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    // ── GET CHAT: Ambil riwayat percakapan ──────
    public function getChat(Request $request, $id_barang)
    {
        $user = $request->user();

        // Ambil semua pesan untuk barang ini yang melibatkan user ini
        $pesan = Chat::where('id_barang', $id_barang)
            ->where(function($q) use ($user) {
                $q->where('id_pengirim', $user->id_user)
                  ->orWhere('id_penerima', $user->id_user);
            })
            ->with('pengirim:id_user,nama,foto_profil')
            ->orderBy('created_at', 'asc')
            ->get();

        // Tandai semua pesan sebagai sudah dibaca
        Chat::where('id_barang', $id_barang)
            ->where('id_penerima', $user->id_user)
            ->update(['sudah_dibaca' => true]);

        return response()->json($pesan);
    }

    // ── KIRIM PESAN TEKS BIASA ──────────────────
    public function kirimPesan(Request $request)
    {
        $request->validate([
            'id_barang'   => 'required|exists:barangs,id_barang',
            'id_penerima' => 'required|exists:users,id_user',
            'isi_pesan'   => 'required|string|max:1000',
        ]);

        $pesan = Chat::create([
            'id_barang'   => $request->id_barang,
            'id_pengirim' => $request->user()->id_user,
            'id_penerima' => $request->id_penerima,
            'isi_pesan'   => $request->isi_pesan,
            'tipe'        => 'text',
        ]);

        return response()->json($pesan->load('pengirim:id_user,nama,foto_profil'), 201);
    }

    // ── BUAT DEAL: Tombol Deal diklik ────────────
    // Ini adalah fungsi terpenting. Gunakan DB::transaction() agar
    // jika salah satu langkah gagal, semua dibatalkan (tidak ada data korup)
    public function buatDeal(Request $request)
    {
        $request->validate([
            'id_barang'         => 'required|exists:barangs,id_barang',
            'id_penjual'        => 'required|exists:users,id_user',
            'final_price'       => 'required|numeric|min:0',
            'metode_pembayaran' => 'required|in:COD,Transfer,Barter',
            'metode_pengiriman' => 'required|in:Ambil Sendiri,Dikirim',
        ]);

        $barang = Barang::findOrFail($request->id_barang);

        // Cek apakah barang masih available
        if ($barang->status !== 'available' && $barang->status !== 'negotiating') {
            return response()->json([
                'message' => 'Barang ini sudah tidak tersedia untuk ditransaksikan.'
            ], 422);
        }

        // GUNAKAN TRANSACTION agar semua langkah atomic
        $result = DB::transaction(function() use ($request, $barang) {

            // LANGKAH 1: Simpan pesan deal di tabel chats
            // Isi pesan berisi JSON data deal — frontend akan parse ini
            $isiDeal = json_encode([
                'final_price'       => $request->final_price,
                'metode_pembayaran' => $request->metode_pembayaran,
                'metode_pengiriman' => $request->metode_pengiriman,
            ]);

            $pesan = Chat::create([
                'id_barang'   => $request->id_barang,
                'id_pengirim' => $request->user()->id_user,
                'id_penerima' => $request->id_penjual,
                'isi_pesan'   => $isiDeal,
                'tipe'        => 'deal', // Tipe khusus!
            ]);

            // LANGKAH 2: Buat record transaksi baru
            $transaksi = Transaksi::create([
                'id_barang'         => $request->id_barang,
                'id_pembeli'        => $request->user()->id_user,
                'id_penjual'        => $request->id_penjual,
                'final_price'       => $request->final_price,
                'metode_pembayaran' => $request->metode_pembayaran,
                'metode_pengiriman' => $request->metode_pengiriman,
                'status_transaksi'  => 'pending',
            ]);

            // LANGKAH 3: Update status barang
            $barang->update(['status' => 'deal_made']);

            return ['pesan' => $pesan, 'transaksi' => $transaksi];
        });

    }

    public function pesanBaru(Request $request, $id_barang)
{
    $afterId = $request->query('after', 0);
    $user = $request->user();

    $pesanBaru = Chat::where('id_barang', $id_barang)
        ->where('id', '>', $afterId)
        ->where(function($q) use ($user) {
            $q->where('id_pengirim', $user->id_user)
              ->orWhere('id_penerima', $user->id_user);
        })
        ->with('pengirim:id_user,nama,foto_profil')
        ->orderBy('created_at', 'asc')
        ->get();

    return response()->json($pesanBaru);
}

}

