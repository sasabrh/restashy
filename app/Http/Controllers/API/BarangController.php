<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BarangController extends Controller
{
    // ── INDEX: Browse dengan filter ─────────────
    public function index(Request $request)
    {
        $query = Barang::visible() // Scope: is_hidden=false, status != sold
                       ->with('penjual:id_user,nama,foto_profil'); // Load data penjual

        // ── FILTER KATEGORI ──────────────────────
        // Request: GET /api/barang?kategori=Dorm+Living
        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        // ── FILTER SEARCH ────────────────────────
        // Request: GET /api/barang?search=laptop
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'LIKE', '%'.$request->search.'%')
                  ->orWhere('deskripsi',  'LIKE', '%'.$request->search.'%');
            });
        }

        // ── FILTER RADIUS 2KM ────────────────────
        // Request: GET /api/barang?lat=-6.91&lng=107.60&radius=2
        // Menggunakan Formula Haversine untuk hitung jarak bola bumi
        if ($request->filled('lat') && $request->filled('lng')) {
            $lat    = $request->lat;
            $lng    = $request->lng;
            $radius = $request->radius ?? 2; // Default 2KM

            $query->selectRaw('*,
                ( 6371 * acos(
                    cos(radians(?)) * cos(radians(latitude))
                    * cos(radians(longitude) - radians(?))
                    + sin(radians(?)) * sin(radians(latitude))
                )) AS jarak_km', [$lat, $lng, $lat])
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->having('jarak_km', '<=', $radius)
                ->orderBy('jarak_km', 'asc'); // Terdekat dulu
        }

        // ── FILTER IS_NEGOTIABLE ─────────────────
        if ($request->filled('negotiable')) {
            $query->where('is_negotiable', true);
        }

        // Paginate 12 barang per halaman
        $barangs = $query->paginate(12);

        return response()->json($barangs);
    }

    // ── SHOW: Detail satu barang ─────────────────
    public function show(Request $request, $id)
    {
        $barang = Barang::with('penjual')->findOrFail($id);

        // LOGIKA HARGA KHUSUS MAHASISWA:
        // Harga khusus HANYA ditampilkan jika user login dan is_student = true
        $user = $request->user(); // Null jika tidak login
        if (!$user || !$user->is_student) {
            $barang->makeHidden('harga_khusus');
        }

        return response()->json($barang);
    }

    // ── STORE: Upload barang baru ─────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang'  => 'required|string|max:255',
            'kategori'     => 'required|in:Academic Stash,Dorm Living,Wardrobe Finds,Free Items,Lainnya',
            'deskripsi'    => 'required|string',
            'harga_normal' => 'required|numeric|min:0',
            'harga_khusus' => 'nullable|numeric|min:0|lt:harga_normal',
            'foto_barang'  => 'nullable|image|max:2048', // Max 2MB
            'latitude'     => 'nullable|numeric',
            'longitude'    => 'nullable|numeric',
            // Spesifikasi WAJIB jika kategori Dorm Living
            'spesifikasi'  => 'required_if:kategori,Dorm Living',
        ]);

        $data = $request->except('foto_barang');
        $data['id_user'] = $request->user()->id_user;

        // Handle upload foto
        if ($request->hasFile('foto_barang')) {
            $path = $request->file('foto_barang')->store('barang', 'public');
            $data['foto_barang'] = $path;
        }

        $barang = Barang::create($data);

        return response()->json([
            'message' => 'Barang berhasil diupload',
            'barang'  => $barang,
        ], 201);
    }
}
