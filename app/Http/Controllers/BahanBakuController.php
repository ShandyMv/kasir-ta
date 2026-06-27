<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Satuan;
use App\Http\Requests\StoreBahanBakuRequest;
use App\Http\Requests\UpdateBahanBakuRequest;
use App\Services\MinMaxAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class BahanBakuController extends Controller
{
    public function index(Request $request, MinMaxAnalysisService $minMax)
    {
        $search = $request->get('search');

        $query = BahanBaku::with('satuan')
            ->when($search, function ($q, $s) {
                $q->where('nama_bahan', 'like', "%{$s}%")
                  ->orWhere('kode_bahan', 'like', "%{$s}%");
            });

        $allIds = $query->pluck('id')->toArray();
        $rmaxMap = $minMax->getRmaxDaily($allIds);
        $rataMap = $minMax->getRataRataDaily($allIds);

        $allBahan = $query->get();
        $processed = collect();
        foreach ($allBahan as $b) {
            $rmax = $rmaxMap[$b->id] ?? 0;
            $rata = $rataMap[$b->id] ?? 0;
            $lt = (int) ($b->lead_time ?? 1);

            $ss = round(($rmax - $rata) * $lt, 2);
            $min = round($rmax * $lt, 2);
            $max = round($ss + $min, 2);

            $b->lead_time_val = $lt;
            $b->rmax_daily = $rmax;
            $b->rata_rata = $rata;
            $b->safety_stock_calc = $ss;
            $b->min_stock_calc = $min;
            $b->max_stock_calc = $max;
            $b->order_qty = round($max - $min, 2);

            $b->status_code = $minMax->analyzeDynamic(
                (float) $b->stok_saat_ini,
                (float) $ss,
                (float) $min,
                (float) $max
            );
            $processed->push($b);
        }

        $statAman = $processed->filter(fn($b) => in_array($b->status_code, ['AMAN', 'BERLEBIH']))->count();
        $statSegera = $processed->filter(fn($b) => $b->status_code === 'SEGERA_ROP')->count();
        $statKritis = $processed->filter(fn($b) => $b->status_code === 'KRITIS')->count();
        $statBerlebih = 0;

        $perPage = 10;
        $page = Paginator::resolveCurrentPage();
        $items = $processed->forPage($page, $perPage)->values();
        $bahanBakus = new LengthAwarePaginator($items, $processed->count(), $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
        $bahanBakus->appends($request->query());

        $satuans = Satuan::all();
        return view('bahan-baku.index', compact(
            'bahanBakus', 'satuans', 'search',
            'statAman', 'statSegera', 'statKritis', 'statBerlebih'
        ));
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
