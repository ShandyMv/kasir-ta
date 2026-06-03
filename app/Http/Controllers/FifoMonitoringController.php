<?php

namespace App\Http\Controllers;

use App\Models\FifoBatch;
use App\Models\BahanBaku;
use Illuminate\Http\Request;

class FifoMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $bahanBakus = BahanBaku::with(['satuan', 'fifoBatches' => function ($q) {
            $q->where('sisa_stok', '>', 0)->orderBy('tanggal_masuk')->orderBy('id');
        }])->when($search, function ($q, $s) {
            $q->where('nama_bahan', 'like', "%{$s}%");
        })->whereHas('fifoBatches', fn($q) => $q->where('sisa_stok', '>', 0))
        ->paginate(10);

        return view('fifo-monitoring.index', compact('bahanBakus', 'search'));
    }
}
