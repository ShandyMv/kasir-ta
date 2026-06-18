<?php

namespace App\Services;

use App\Models\BahanBaku;
use App\Models\StokKeluar;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class MinMaxAnalysisService
{
    public function analyze(BahanBaku $bahan): string
    {
        $stok = $bahan->stok_saat_ini;
        $safety = $bahan->safety_stock;
        $min = $bahan->stok_minimum;
        $max = $bahan->stok_maksimum;

        if ($stok > $max) {
            return 'BERLEBIH';
        }
        if ($stok > $min) {
            return 'AMAN';
        }
        if ($stok > $safety) {
            return 'SEGERA_ROP';
        }
        return 'KRITIS';
    }

    public function getAllStatus(): Collection
    {
        return BahanBaku::with('satuan')->get()->map(function ($b) {
            $b->status = $this->analyze($b);
            return $b;
        });
    }

    public function getKritis(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini <= safety_stock')->get();
    }

    public function getSegeraROP(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini > safety_stock AND stok_saat_ini <= stok_minimum')->get();
    }

    public function getAman(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_minimum AND stok_saat_ini <= stok_maksimum')->get();
    }

    public function getBerlebih(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_maksimum')->get();
    }

    public function countKritis(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini <= safety_stock')->count();
    }

    public function countSegeraROP(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini > safety_stock AND stok_saat_ini <= stok_minimum')->count();
    }

    public function countAman(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_minimum AND stok_saat_ini <= stok_maksimum')->count();
    }

    public function countBerlebih(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_maksimum')->count();
    }

    public function countRestock(): int
    {
        return $this->countKritis() + $this->countSegeraROP();
    }

    public function getRecommendations(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini <= stok_minimum')
            ->orderBy('stok_saat_ini')
            ->get();
    }

    public function getRmaxDaily(array $bahanIds): array
    {
        if (empty($bahanIds)) {
            return [];
        }

        $daily = StokKeluar::select(
            'bahan_baku_id',
            DB::raw('DATE(tanggal_keluar) as tgl'),
            DB::raw('SUM(jumlah_keluar) as daily_total')
        )
            ->whereIn('bahan_baku_id', $bahanIds)
            ->groupBy('bahan_baku_id', 'tgl');

        $result = DB::table(DB::raw("({$daily->toSql()}) as daily"))
            ->mergeBindings($daily->getQuery())
            ->select('bahan_baku_id', DB::raw('MAX(daily_total) as rmax_daily'))
            ->groupBy('bahan_baku_id')
            ->pluck('rmax_daily', 'bahan_baku_id')
            ->toArray();

        return array_map(fn($v) => (float) $v, $result);
    }

    public function getRataRataDaily(array $bahanIds): array
    {
        if (empty($bahanIds)) {
            return [];
        }

        $daily = StokKeluar::select(
            'bahan_baku_id',
            DB::raw('DATE(tanggal_keluar) as tgl'),
            DB::raw('SUM(jumlah_keluar) as daily_total')
        )
            ->whereIn('bahan_baku_id', $bahanIds)
            ->groupBy('bahan_baku_id', 'tgl');

        $result = DB::table(DB::raw("({$daily->toSql()}) as daily"))
            ->mergeBindings($daily->getQuery())
            ->select('bahan_baku_id', DB::raw('AVG(daily_total) as rata_rata'))
            ->groupBy('bahan_baku_id')
            ->pluck('rata_rata', 'bahan_baku_id')
            ->toArray();

        return array_map(fn($v) => round((float) $v, 2), $result);
    }

    public function analyzeDynamic(float $stok, float $safety, float $min, float $max): string
    {
        if ($stok > $max) {
            return 'BERLEBIH';
        }
        if ($stok > $min) {
            return 'AMAN';
        }
        if ($stok > $safety) {
            return 'SEGERA_ROP';
        }
        return 'KRITIS';
    }
}
