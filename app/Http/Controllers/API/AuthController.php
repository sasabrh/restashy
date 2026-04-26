<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // ── REGISTER ────────────────────────────────
    public function register(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users',
            'password'=> 'required|min:8|confirmed', // Butuh password_confirmation
            'kontak'  => 'nullable|string',
        ]);

        // LOGIKA VERIFIED STUDENT:
        // Daftar domain email kampus yang diizinkan
        $kampusDomains = [
            'student.univ.ac.id',
            'mahasiswa.itb.ac.id',
            'student.ui.ac.id',
            // Tambahkan domain kampus kamu di sini
        ];

        $emailDomain = substr(strrchr($request->email, '@'), 1);
        $isStudent = in_array($emailDomain, $kampusDomains);

        $user = User::create([
            'nama'       => $request->nama,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'kontak'     => $request->kontak,
            'is_student' => $isStudent, // Otomatis true jika email kampus
        ]);

        $token = $user->createToken('restashy-token')->plainTextToken;

        return response()->json([
            'message'    => 'Registrasi berhasil',
            'user'       => $user,
            'token'      => $token,
            'is_student' => $isStudent, // Kirim ke frontend untuk tampilkan badge
        ], 201);
    }

    // ── LOGIN ────────────────────────────────────
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        if ($user->is_banned) {
            return response()->json(['message' => 'Akun diblokir.'], 403);
        }

        // Hapus token lama, buat yang baru
        $user->tokens()->delete();
        $token = $user->createToken('restashy-token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil',
            'user'    => $user,
            'token'   => $token,
        ]);
    }

    // ── LOGOUT ───────────────────────────────────
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logout berhasil']);
    }

    // ── GET PROFILE ──────────────────────────────
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
