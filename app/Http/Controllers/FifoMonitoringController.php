<?php

namespace App\Http\Controllers;

use App\Models\FifoBatch;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class FifoMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter');

        $query = FifoBatch::with('bahanBaku.satuan')
            ->where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk')
            ->orderBy('id');

        if ($search) {
            $query->whereHas('bahanBaku', function ($q) use ($search) {
                $q->where('nama_bahan', 'like', "%{$search}%");
            });
        }

        $allBatches = $query->get();

        $now = now();
        $processed = collect();
        foreach ($allBatches as $i => $batch) {
            $bahan = $batch->bahanBaku;
            $expiry = $bahan->hari_kedaluwarsa ?? 30;
            $tglKadaluwarsa = $batch->tanggal_masuk->copy()->addDays($expiry);
            $sisaHari = (int) $now->diffInDays($tglKadaluwarsa, false);
            $pct = $expiry > 0 ? (($expiry - max(0, $sisaHari)) / $expiry) * 100 : 100;

            if ($sisaHari <= 0) $indicator = 'expired';
            elseif ($pct > 75) $indicator = 'kritis';
            elseif ($pct > 50) $indicator = 'waspada';
            else $indicator = 'normal';

            $batch->prioritas = $i + 1;
            $batch->sisa_umur = $sisaHari;
            $batch->indicator = $indicator;
            $batch->tgl_kadaluwarsa = $tglKadaluwarsa;

            $processed->push($batch);
        }

        // Build batch groups for detail modals
        foreach ($allBatches as $i => $batch) {
            $bahan = $batch->bahanBaku;
            $expiry = $bahan->hari_kedaluwarsa ?? 30;
            $tglKadaluwarsa = $batch->tanggal_masuk->copy()->addDays($expiry);
            $sisaHari = (int) $now->diffInDays($tglKadaluwarsa, false);
            $pct = $expiry > 0 ? (($expiry - max(0, $sisaHari)) / $expiry) * 100 : 100;
            if ($sisaHari <= 0) $ind = 'expired';
            elseif ($pct > 75) $ind = 'kritis';
            elseif ($pct > 50) $ind = 'waspada';
            else $ind = 'normal';

            $bid = $bahan->id;
            if (!isset($batchGroups[$bid])) {
                $batchGroups[$bid] = [
                    'nama' => $bahan->nama_bahan,
                    'expiry' => $expiry,
                    'total_stok' => 0,
                    'satuan' => $bahan->satuan->nama_satuan,
                    'batches' => [],
                ];
            }
            $batchGroups[$bid]['total_stok'] += (float) $batch->sisa_stok;
            $batchGroups[$bid]['batches'][] = [
                'batch_kode' => $batch->batch_kode,
                'prioritas' => $i + 1,
                'tgl_masuk' => $batch->tanggal_masuk->format('d M Y'),
                'tgl_kadaluarsa' => $tglKadaluwarsa->format('d M Y'),
                'sisa_stok' => (float) $batch->sisa_stok,
                'sisa_umur' => $sisaHari > 0 ? $sisaHari . ' hr' : 'sudah ' . abs($sisaHari) . ' hr',
                'indicator' => $ind,
            ];
        }

        if ($filter) {
            $processed = $processed->filter(fn($b) => $b->indicator === $filter);
        }

        $kritis = $processed->filter(fn($b) => $b->indicator === 'kritis')->count();
        $waspada = $processed->filter(fn($b) => $b->indicator === 'waspada')->count();
        $normal = $processed->filter(fn($b) => $b->indicator === 'normal')->count();
        $expired = $processed->filter(fn($b) => $b->indicator === 'expired')->count();

        $perPage = 20;
        $page = Paginator::resolveCurrentPage();
        $total = $processed->count();
        $items = $processed->slice(($page - 1) * $perPage, $perPage)->values();
        $batches = new LengthAwarePaginator($items, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
        ]);
        $batches->appends($request->query());

        return view('fifo-monitoring.index', compact('batches', 'search', 'filter', 'kritis', 'waspada', 'normal', 'expired', 'batchGroups'));
    }
}
