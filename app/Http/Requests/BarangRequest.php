<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class BarangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */

    public function rules(): array
    {
        return [
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'required',
            'harga_umum' => 'required|numeric',
            'harga_mahasiswa' => 'required|numeric',
            'kategori' => 'required|in:elektronik,perabot,buku,pakaian,lainnya',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'foto' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ];
    }
}
