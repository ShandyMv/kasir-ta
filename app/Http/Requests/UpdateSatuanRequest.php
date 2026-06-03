<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSatuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('satuan');
        return [
            'nama_satuan' => 'required|string|max:100|unique:satuans,nama_satuan,' . $id,
        ];
    }

    public function messages(): array
    {
        return [
            'nama_satuan.required' => 'Nama satuan wajib diisi.',
            'nama_satuan.unique' => 'Nama satuan sudah digunakan.',
        ];
    }
}
