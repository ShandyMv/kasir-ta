<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBahanBakuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_bahan' => 'required|string|max:255',
            'satuan_id' => 'required|exists:satuans,id',
            'stok_minimum' => 'nullable|numeric|min:0',
            'stok_maksimum' => 'nullable|numeric|min:0|prohibited_if:stok_minimum,null',
            'lead_time' => 'nullable|integer|min:1',
            'hari_kedaluwarsa' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan_id.required' => 'Satuan wajib dipilih.',
            'satuan_id.exists' => 'Satuan tidak valid.',
            'stok_maksimum.prohibited_if' => 'Stok maksimum tidak boleh diisi jika stok minimum kosong.',
        ];
    }
}
