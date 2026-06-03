<?php

namespace App\Http\Controllers;

use App\Models\Satuan;
use App\Http\Requests\StoreSatuanRequest;
use App\Http\Requests\UpdateSatuanRequest;
use Illuminate\Http\Request;

class SatuanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $satuans = Satuan::when($search, function ($q, $s) {
            $q->where('nama_satuan', 'like', "%{$s}%");
        })->latest()->paginate(10);

        $totalSatuan = Satuan::count();
        $totalBahan = \App\Models\BahanBaku::count();

        return view('satuan.index', compact('satuans', 'search', 'totalSatuan', 'totalBahan'));
    }

    public function store(StoreSatuanRequest $request)
    {
        Satuan::create($request->validated());
        return redirect()->route('satuan')->with('success', 'Satuan berhasil ditambahkan.');
    }

    public function update(UpdateSatuanRequest $request, Satuan $satuan)
    {
        $satuan->update($request->validated());
        return redirect()->route('satuan')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy(Satuan $satuan)
    {
        if ($satuan->bahanBakus()->exists()) {
            return redirect()->route('satuan')->with('error', 'Satuan tidak bisa dihapus karena masih digunakan oleh bahan baku.');
        }
        $satuan->delete();
        return redirect()->route('satuan')->with('success', 'Satuan berhasil dihapus.');
    }
}
