<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    // [GET] Ambil semua barang + Filter Search & Radius
    public function index(Request $request)
    {
        $query = Barang::with('user');

        // Filter Nama
        if ($request->has('search')) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        // Filter Kategori
        if ($request->has('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // Filter Radius (Haversine Formula)
        if ($request->lat && $request->long && $request->radius) {
            $lat = $request->lat;
            $long = $request->long;
            $radius = $request->radius;

            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance", [$lat, $long, $lat])
                  ->having('distance', '<=', $radius)
                  ->orderBy('distance', 'asc');
        } else {
            $query->latest();
        }

        return response()->json([
            'success' => true,
            'data' => $query->get()
        ]);
    }

    // [POST] Tambah barang baru (Plus Upload Foto)
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_barang'     => 'required',
            'deskripsi'       => 'required',
            'harga_umum'      => 'required|integer',
            'harga_mahasiswa' => 'required|integer',
            'kategori'        => 'required|in:elektronik,perabot,buku,pakaian,lainnya',
            'status'          => 'required|in:available,sold,pending',
            'is_negotiable'   => 'required|boolean',
            'lat'             => 'required|numeric',
            'long'            => 'required|numeric',
            'foto'            => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validasi foto
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $data = $request->all();
        
        // 1. Ambil user_id dari token yang sedang login (lebih aman)
        $data['user_id'] = auth()->id() ?? $request->user_id;

        // 2. Logika Simpan Foto
        if ($request->hasFile('foto')) {
            $image = $request->file('foto');
            $image->storeAs('public/barang', $image->hashName());
            $data['foto'] = $image->hashName();
        }

        $barang = Barang::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Barang Berhasil Disimpan!',
            'data'    => $barang
        ], 201);
    }

    // [GET] Detail 1 barang
public function show($id)
{
    $barang = Barang::with('user')->find($id);

    if (!$barang) {
        return response()->json(['message' => 'Barang Tidak Ditemukan!'], 404);
    }

    // Logika harga otomatis berdasarkan status mahasiswa
    $user = auth('sanctum')->user();
    $tampilkanHargaKhusus = $user && $user->is_mahasiswa; 

    return response()->json([
        'success' => true,
        'data' => [
            'id' => $barang->id,
            'nama_barang' => $barang->nama_barang,
            'deskripsi' => $barang->deskripsi,
            'harga_tampil' => $tampilkanHargaKhusus ? $barang->harga_mahasiswa : $barang->harga_umum,
            'is_student_price' => $tampilkanHargaKhusus,
            'penjual' => $barang->user->name,
            'foto' => url('storage/barang/' . $barang->foto),
        ]
    ]);
}

    // [PUT] Update barang
    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);
        if (!$barang) return response()->json(['message' => 'Tidak ditemukan'], 404);

        $barang->update($request->all());
        return response()->json(['success' => true, 'data' => $barang]);
    }

    // [DELETE] Hapus barang
    public function destroy($id)
    {
        $barang = Barang::find($id);
        if (!$barang) return response()->json(['message' => 'Tidak ditemukan'], 404);

        // Hapus foto dari storage jika ada
        Storage::delete('public/barang/'.$barang->foto);
        $barang->delete();

        return response()->json(['success' => true, 'message' => 'Terhapus']);
    }

    public function userBarang($id)
    {
        $barangs = Barang::where('user_id', $id)->get();
        return response()->json(['success' => true, 'data' => $barangs]);
    }
}