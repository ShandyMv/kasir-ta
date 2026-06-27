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
        $rataData = $this->minMax->getRataRataDaily($allBahanIds);

        $allBahan = $query->get();

        $processed = collect();
        foreach ($allBahan as $b) {
            $rmax = $rmaxData[$b->id] ?? 0;
            $rata = $rataData[$b->id] ?? 0;
            $lt = $b->lead_time;

            $b->lead_time_val = $lt;
            $b->rmax_daily = $rmax;
            $b->rata_rata = $rata;

            $ss = round(($rmax - $rata) * $lt, 2);
            $min = round($rmax * $lt, 2);
            $max = round($ss + ($rmax * $lt), 2);

            $b->safety_stock_calc = $ss;
            $b->min_stock_calc = $min;
            $b->max_stock_calc = $max;
            $b->order_qty = round($max - $min, 2);

            $b->status_code = $this->minMax->analyzeDynamic(
                (float) $b->stok_saat_ini,
                (float) $ss,
                (float) $min,
                (float) $max
            );
            $processed->push($b);
        }

        if ($status) {
            $processed = $processed->filter(function ($b) use ($status) {
                if ($status === 'AMAN') {
                    return in_array($b->status_code, ['AMAN', 'BERLEBIH']);
                }
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

        $statAman = $processed->filter(fn($b) => in_array($b->status_code, ['AMAN', 'BERLEBIH']))->count();
        $statSegera = $processed->filter(fn($b) => $b->status_code === 'SEGERA_ROP')->count();
        $statKritis = $processed->filter(fn($b) => $b->status_code === 'KRITIS')->count();

        return view('min-max-analysis.index', compact(
            'bahanBakus', 'search', 'status',
            'statAman', 'statSegera', 'statKritis'
        ));
    }

    public function apply(Request $request, BahanBaku $bahanBaku)
    {
        $data = $request->validate([
            'min_stock' => 'required|numeric|min:0',
            'max_stock' => 'required|numeric|min:0|gte:min_stock',
        ]);

        $bahanBaku->update([
            'stok_minimum' => $data['min_stock'],
            'stok_maksimum' => $data['max_stock'],
        ]);

        return back()->with('success', 'Stok minimum & maksimum berhasil diterapkan.');
    }
}
