<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStokMasukRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bahan_baku_id' => 'required|exists:bahan_bakus,id',
            'supplier_id' => 'required|exists:suppliers,id',
            'jumlah' => 'required|numeric|min:0.01',
            'tanggal_masuk' => 'required|date',
            'keterangan' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'bahan_baku_id.required' => 'Bahan baku wajib dipilih.',
            'supplier_id.required' => 'Supplier wajib dipilih.',
            'jumlah.required' => 'Jumlah wajib diisi.',
            'jumlah.min' => 'Jumlah harus lebih dari 0.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
        ];
    }
}
