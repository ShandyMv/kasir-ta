<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use Illuminate\Http\Request;

class MinMaxAnalysisController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $bahanBakus = BahanBaku::with('satuan')
            ->when($search, function ($q, $s) {
                $q->where('nama_bahan', 'like', "%{$s}%");
            })
            ->when($status, function ($q, $s) {
                if ($s === 'restock') {
                    $q->whereColumn('stok_saat_ini', '<', 'stok_minimum');
                } elseif ($s === 'aman') {
                    $q->whereColumn('stok_saat_ini', '>=', 'stok_minimum')
                      ->whereColumn('stok_saat_ini', '<=', 'stok_maksimum');
                } elseif ($s === 'berlebih') {
                    $q->whereColumn('stok_saat_ini', '>', 'stok_maksimum');
                }
            })->paginate(10);

        return view('min-max-analysis.index', compact('bahanBakus', 'search', 'status'));
    }
}
