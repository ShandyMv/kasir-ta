<?php

namespace App\Services;

use App\Models\BahanBaku;
use Illuminate\Database\Eloquent\Collection;

class MinMaxAnalysisService
{
    public function analyze(BahanBaku $bahan): string
    {
        if ($bahan->stok_saat_ini < $bahan->stok_minimum) {
            return 'RESTOCK';
        }
        if ($bahan->stok_saat_ini > $bahan->stok_maksimum) {
            return 'BERLEBIH';
        }
        return 'AMAN';
    }

    public function getAllStatus(): Collection
    {
        return BahanBaku::with('satuan')->get()->map(function ($b) {
            $b->status = $this->analyze($b);
            return $b;
        });
    }

    public function getAman(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini BETWEEN stok_minimum AND stok_maksimum')->get();
    }

    public function getRestock(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini < stok_minimum')->get();
    }

    public function getBerlebih(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_maksimum')->get();
    }

    public function countAman(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini BETWEEN stok_minimum AND stok_maksimum')->count();
    }

    public function countRestock(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini < stok_minimum')->count();
    }

    public function countBerlebih(): int
    {
        return BahanBaku::whereRaw('stok_saat_ini > stok_maksimum')->count();
    }

    public function getRecommendations(): Collection
    {
        return BahanBaku::whereRaw('stok_saat_ini < stok_minimum')
            ->orderByRaw('(stok_saat_ini / stok_minimum)')
            ->get();
    }
}
