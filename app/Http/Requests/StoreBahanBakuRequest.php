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
            'stok_minimum' => 'required|numeric|min:0',
            'stok_maksimum' => 'required|numeric|min:0|gte:stok_minimum',
            'lead_time' => 'nullable|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_bahan.required' => 'Nama bahan wajib diisi.',
            'satuan_id.required' => 'Satuan wajib dipilih.',
            'satuan_id.exists' => 'Satuan tidak valid.',
            'stok_minimum.required' => 'Stok minimum wajib diisi.',
            'stok_maksimum.required' => 'Stok maksimum wajib diisi.',
            'stok_maksimum.gte' => 'Stok maksimum harus lebih besar atau sama dengan stok minimum.',
        ];
    }
}
