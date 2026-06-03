<?php

namespace App\Http\Controllers;

use App\Models\StokMasuk;
use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\FifoBatch;
use App\Http\Requests\StoreStokMasukRequest;
use Illuminate\Http\Request;

class StokMasukController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stokMasuks = StokMasuk::with(['bahanBaku', 'supplier', 'user'])
            ->when($search, function ($q, $s) {
                $q->whereHas('bahanBaku', fn($q) => $q->where('nama_bahan', 'like', "%{$s}%"))
                  ->orWhere('batch_kode', 'like', "%{$s}%");
            })->latest()->paginate(10);

        $bahanBakus = BahanBaku::all();
        $suppliers = Supplier::all();
        return view('stok-masuk.index', compact('stokMasuks', 'bahanBakus', 'suppliers', 'search'));
    }

    public function store(StoreStokMasukRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();
        $data['batch_kode'] = 'BATCH-' . now()->format('ymd') . '-' . strtoupper(substr(uniqid(), -3));

        $stokMasuk = StokMasuk::create($data);

        FifoBatch::create([
            'bahan_baku_id' => $data['bahan_baku_id'],
            'stok_masuk_id' => $stokMasuk->id,
            'batch_kode' => $data['batch_kode'],
            'jumlah_awal' => $data['jumlah'],
            'sisa_stok' => $data['jumlah'],
            'tanggal_masuk' => $data['tanggal_masuk'],
        ]);

        $bahan = BahanBaku::find($data['bahan_baku_id']);
        $bahan->increment('stok_saat_ini', $data['jumlah']);

        return redirect()->route('stok-masuk')->with('success', 'Stok masuk berhasil dicatat.');
    }
}
