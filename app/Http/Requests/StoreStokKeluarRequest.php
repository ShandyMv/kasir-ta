<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokKeluarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'jumlah_keluar' => 'required|numeric|min:0.01',
            'tanggal_keluar' => 'required|date',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_baku_id.required' => 'Bahan baku wajib dipilih.',
            'jumlah_keluar.required' => 'Jumlah keluar wajib diisi.',
            'jumlah_keluar.min' => 'Jumlah keluar harus lebih dari 0.',
            'tanggal_keluar.required' => 'Tanggal keluar wajib diisi.',
        ];
    }
}
