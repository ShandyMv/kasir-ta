<?php

namespace App\Http\Controllers;

use App\Models\StokKeluar;
use App\Models\BahanBaku;
use App\Models\FifoBatch;
use App\Http\Requests\StoreStokKeluarRequest;
use App\Services\FIFOService;
use Illuminate\Http\Request;

class StokKeluarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $stokKeluars = StokKeluar::with(['bahanBaku', 'user', 'details.fifoBatch'])
            ->when($search, function ($q, $s) {
                $q->whereHas('bahanBaku', fn($q) => $q->where('nama_bahan', 'like', "%{$s}%"));
            })->latest()->paginate(10);

        $bahanBakus = BahanBaku::all();
        return view('stok-keluar.index', compact('stokKeluars', 'bahanBakus', 'search'));
    }

    public function create(Request $request, FIFOService $fifoService)
    {
        $bahanBakuId = $request->get('bahan_baku_id');
        $bahanBakus = BahanBaku::all();
        $availableBatches = collect();
        $expiredBatches = collect();

        if ($bahanBakuId) {
            $availableBatches = $fifoService->getAvailableBatches($bahanBakuId);
            $expiredBatches = FifoBatch::with('bahanBaku')
                ->where('bahan_baku_id', $bahanBakuId)
                ->where('sisa_stok', '>', 0)
                ->whereHas('bahanBaku', fn($q) => $q->whereNotNull('hari_kedaluwarsa'))
                ->get()
                ->filter(fn($fb) => $fb->tanggal_masuk->addDays($fb->bahanBaku->hari_kedaluwarsa)->isPast())
                ->values();
        }

        return view('stok-keluar.create', compact(
            'bahanBakus', 'availableBatches', 'expiredBatches', 'bahanBakuId'
        ));
    }

    public function store(StoreStokKeluarRequest $request, FIFOService $fifoService)
    {
        $data = $request->validated();

        if (!$fifoService->isStockSufficient($data['bahan_baku_id'], $data['jumlah_keluar'])) {
            return back()->with('error', 'Stok tersedia tidak mencukupi.')->withInput();
        }

        $fifoService->consumeStock($data);

        return redirect()->route('stok-keluar')->with('success', 'Stok keluar berhasil dicatat.');
    }
}
