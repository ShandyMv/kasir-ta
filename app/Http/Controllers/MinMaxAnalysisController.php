<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Services\MinMaxAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MinMaxAnalysisController extends Controller
{
    protected $minMax;

    public function __construct(MinMaxAnalysisService $minMax)
    {
        $this->minMax = $minMax;
    }

    public function index(Request $request)
    {
        $search = $request->get('search');
        $status = $request->get('status');

        $query = BahanBaku::with('satuan')
            ->when($search, function ($q, $s) {
                $q->where('nama_bahan', 'like', "%{$s}%");
            });

        $allBahanIds = $query->pluck('id')->toArray();
        $rmaxData = $this->minMax->getRmaxDaily($allBahanIds);

        $allBahan = $query->get();

        $processed = collect();
        foreach ($allBahan as $b) {
            $rmax = $rmaxData[$b->id] ?? 0;
            $b->rmax_daily = $rmax;
            $b->safety_stock_calc = round($rmax * $b->lead_time, 2);
            $b->reorder_point_calc = round($b->stok_minimum + $b->safety_stock_calc, 2);
            $b->status_code = $this->minMax->analyzeDynamic(
                (float) $b->stok_saat_ini,
                (float) $b->safety_stock_calc,
                (float) $b->reorder_point_calc,
                (float) $b->stok_maksimum
            );
            $processed->push($b);
        }

        if ($status) {
            $processed = $processed->filter(function ($b) use ($status) {
                return $b->status_code === $status;
            });
        }

        $perPage = 10;
        $page = Paginator::resolveCurrentPage();
        $total = $processed->count();
        $items = $processed->slice(($page - 1) * $perPage, $perPage)->values();
        $bahanBakus = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);

        $bahanBakus->appends($request->query());

        $statAman = $processed->filter(fn($b) => $b->status_code === 'AMAN')->count();
        $statSegera = $processed->filter(fn($b) => $b->status_code === 'SEGERA_ROP')->count();
        $statKritis = $processed->filter(fn($b) => $b->status_code === 'KRITIS')->count();
        $statBerlebih = $processed->filter(fn($b) => $b->status_code === 'BERLEBIH')->count();

        return view('min-max-analysis.index', compact(
            'bahanBakus', 'search', 'status',
            'statAman', 'statSegera', 'statKritis', 'statBerlebih'
        ));
    }
}
