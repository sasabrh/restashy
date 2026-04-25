<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class WishlistController extends Controller
{
    // Lihat semua barang di wishlist saya
    public function index()
    {
        $wishlists = Wishlist::with('barang')
            ->where('user_id', auth()->id())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $wishlists
        ]);
    }

    // Tambah atau Hapus dari wishlist (Toggle)
    public function store(Request $request)
    {
        $request->validate(['barang_id' => 'required|exists:barangs,id']);

        $exists = Wishlist::where('user_id', auth()->id())
            ->where('barang_id', $request->barang_id)
            ->first();

        if ($exists) {
            $exists->delete();
            return response()->json(['message' => 'Dihapus dari wishlist']);
        }

        $wishlist = Wishlist::create([
            'user_id' => auth()->id(),
            'barang_id' => $request->barang_id
        ]);

        return response()->json(['message' => 'Berhasil ditambah ke wishlist', 'data' => $wishlist]);
    }
}
