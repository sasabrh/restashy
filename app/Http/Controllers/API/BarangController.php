<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BarangController extends Controller
{
    // [GET] /api/barang -> Ambil semua data barang
public function index(Request $request)
{
    // Start query
    $query = Barang::with('user');

    // 1. Filter Search (Nama Barang)
    if ($request->has('search')) {
        $query->where('nama_barang', 'like', '%' . $request->search . '%');
    }

    // 2. Filter Kategori
    if ($request->has('kategori')) {
        $query->where('kategori', $request->kategori);
    }

    // 3. Filter Radius (Lokasi Terdekat)
    // Rumus Haversine sederhana untuk hitung jarak koordinat
    if ($request->has('lat') && $request->has('long') && $request->has('radius')) {
        $lat = $request->lat;
        $long = $request->long;
        $radius = $request->radius; // dalam kilometer

        $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance", [$lat, $long, $lat])
              ->having('distance', '<=', $radius)
              ->orderBy('distance', 'asc');
    } else {
        $query->latest();
    }

    $barangs = $query->get();

    return response()->json([
        'success' => true,
        'message' => 'Daftar barang ReStashy',
        'data' => $barangs
    ]);
}

    // [POST] /api/barang -> Tambah barang baru
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'         => 'required|exists:users,id',
            'nama_barang'     => 'required',
            'deskripsi'       => 'required',
            'harga_umum'      => 'required|integer',
            'harga_mahasiswa' => 'required|integer',
            'kategori'        => 'required|in:elektronik,perabot,buku,pakaian,lainnya',
            'status'          => 'required|in:available,sold,pending',
            'is_negotiable'   => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $barang = Barang::create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Barang Berhasil Disimpan!',
            'data'    => $barang
        ], 201);
    }

    // [GET] /api/barang/{id} -> Detail 1 barang
    public function show($id)
    {
        $barang = Barang::with('user')->find($id);

        if ($barang) {
            return response()->json([
                'success' => true,
                'message' => 'Detail Data Barang',
                'data'    => $barang
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Barang Tidak Ditemukan!',
        ], 404);
    }

    // [PUT] /api/barang/{id} -> Update data barang
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Tidak Ditemukan!',
            ], 404);
        }

        $barang->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Barang Berhasil Diupdate!',
            'data'    => $barang
        ], 200);
    }

    // [DELETE] /api/barang/{id} -> Hapus barang
    public function destroy($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json([
                'success' => false,
                'message' => 'Barang Tidak Ditemukan!',
            ], 404);
        }

        $barang->delete();

        return response()->json([
            'success' => true,
            'message' => 'Barang Berhasil Dihapus!',
        ], 200);
    }

    public function userBarang($id)
{
    $barangs = Barang::where('user_id', $id)->get();
    
    return response()->json([
        'success' => true,
        'message' => 'Daftar barang milik user ID: ' . $id,
        'data' => $barangs
    ]);
}
}