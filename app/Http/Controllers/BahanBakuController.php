<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Satuan;
use App\Http\Requests\StoreBahanBakuRequest;
use App\Http\Requests\UpdateBahanBakuRequest;
use Illuminate\Http\Request;

class BahanBakuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bahanBakus = BahanBaku::with('satuan')
            ->when($search, function ($q, $s) {
                $q->where('nama_bahan', 'like', "%{$s}%")
                  ->orWhere('kode_bahan', 'like', "%{$s}%");
            })->latest()->paginate(10);

        $satuans = Satuan::all();
        return view('bahan-baku.index', compact('bahanBakus', 'satuans', 'search'));
    }

    public function store(StoreBahanBakuRequest $request)
    {
        $last = BahanBaku::withTrashed()->latest('id')->first();
        $nextId = $last ? $last->id + 1 : 1;
        $kode = 'BB' . str_pad($nextId, 3, '0', STR_PAD_LEFT);

        $data = $request->validated();
        $data['kode_bahan'] = $kode;
        $data['stok_saat_ini'] = 0;
        $data['stok_minimum'] ??= 0;
        $data['stok_maksimum'] ??= 0;

        BahanBaku::create($data);
        return redirect()->route('bahan-baku')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(UpdateBahanBakuRequest $request, BahanBaku $bahanBaku)
    {
        $bahanBaku->update($request->validated());
        return redirect()->route('bahan-baku')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(BahanBaku $bahanBaku)
    {
        if ($bahanBaku->stokMasuks()->exists() || $bahanBaku->stokKeluars()->exists()) {
            return redirect()->route('bahan-baku')->with('error', 'Bahan baku tidak bisa dihapus karena memiliki riwayat transaksi.');
        }
        $bahanBaku->delete();
        return redirect()->route('bahan-baku')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
