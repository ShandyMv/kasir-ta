<?php

namespace App\Services;

use App\Models\FifoBatch;
use App\Models\StokKeluar;
use App\Models\StokKeluarDetail;
use App\Models\BahanBaku;
use Illuminate\Support\Facades\DB;

class FIFOService
{
    public function getAvailableBatches(int $bahanBakuId)
    {
        return FifoBatch::where('bahan_baku_id', $bahanBakuId)
            ->where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk')
            ->orderBy('id')
            ->get();
    }

    public function isStockSufficient(int $bahanBakuId, float $jumlah): bool
    {
        $totalSisa = FifoBatch::where('bahan_baku_id', $bahanBakuId)
            ->where('sisa_stok', '>', 0)
            ->sum('sisa_stok');

        return $totalSisa >= $jumlah;
    }

    public function consumeStock(array $data): StokKeluar
    {
        $data['user_id'] = auth()->id();
        $sisaKebutuhan = $data['jumlah_keluar'];
        $batches = $this->getAvailableBatches($data['bahan_baku_id']);

        if ($batches->sum('sisa_stok') < $sisaKebutuhan) {
            throw new \RuntimeException('Stok tersedia tidak mencukupi.');
        }

        return DB::transaction(function () use ($data, $batches, $sisaKebutuhan) {
            $stokKeluar = StokKeluar::create($data);

            foreach ($batches as $batch) {
                if ($sisaKebutuhan <= 0) break;

                $ambil = min($batch->sisa_stok, $sisaKebutuhan);

                StokKeluarDetail::create([
                    'stok_keluar_id' => $stokKeluar->id,
                    'fifo_batch_id' => $batch->id,
                    'jumlah_ambil' => $ambil,
                ]);

                $batch->decrement('sisa_stok', $ambil);
                $sisaKebutuhan -= $ambil;
            }

            BahanBaku::find($data['bahan_baku_id'])
                ->decrement('stok_saat_ini', $data['jumlah_keluar']);

            return $stokKeluar;
        });
    }
}
