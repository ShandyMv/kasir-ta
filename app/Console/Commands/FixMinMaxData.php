<?php

namespace App\Console\Commands;

use App\Models\BahanBaku;
use App\Models\FifoBatch;
use App\Models\StokKeluar;
use App\Models\StokKeluarDetail;
use App\Models\StokMasuk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixMinMaxData extends Command
{
    protected $signature = 'fix:minmax-data';
    protected $description = 'Generate realistic transactions & recalculate Min-Max values';

    private $userId = 1;

    public function handle()
    {
        $this->info('=== MENGHAPUS DATA TRANSAKSI LAMA ===');
        $this->truncateTransactionData();

        $this->info("\n=== MEMBUAT DATA REALISTIS ===\n");
        $this->generateRealisticData();

        $this->newLine();
        $this->info('✅ Selesai! Data konsumsi & stok sudah sinkron.');
    }

    private function truncateTransactionData()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        StokKeluarDetail::truncate();
        StokKeluar::truncate();
        FifoBatch::truncate();
        StokMasuk::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function generateRealisticData()
    {
        $profiles = $this->getProfiles();
        $startDate = '2026-06-03';
        $endDate = '2026-06-16';

        foreach ($profiles as $kode => $p) {
            $bahan = BahanBaku::where('kode_bahan', $kode)->first();
            if (!$bahan) continue;

            $this->line("  {$bahan->nama_bahan} ({$kode})...");

            $supplierId = $p['supplier'];
            $dailyRange = $p['range'];
            $frequency = $p['freq'];
            $targetStatus = $p['target'];

            $period = new \DatePeriod(
                new \DateTime($startDate),
                new \DateInterval('P1D'),
                (new \DateTime($endDate))->modify('+1 day')
            );

            // 1. Pre-generate daily consumption
            $dailyConsumptions = [];
            foreach ($period as $date) {
                $dayOfWeek = (int) $date->format('N');
                if ($dayOfWeek <= $frequency) {
                    $dailyConsumptions[$date->format('Y-m-d')] = rand($dailyRange[0], $dailyRange[1]);
                }
            }

            // 2. Calculate stats from generated data
            $totalConsumption = array_sum($dailyConsumptions);
            $consumptionDays = count($dailyConsumptions);
            $rmax = $consumptionDays > 0 ? max($dailyConsumptions) : 0;
            $rata = $consumptionDays > 0 ? round($totalConsumption / $consumptionDays, 2) : 0;
            $lt = $bahan->lead_time ?? 1;

            $ss = round(($rmax - $rata) * $lt, 2);
            $min = round($rmax * $lt, 2);
            $max = round($ss + ($rmax * $lt), 2);

            // 3. Determine desired final stock
            $finalStock = match ($targetStatus) {
                'KRITIS' => max(0, $ss > 0 ? (int) round($ss * 0.3) : 0),
                'SEGERA_ROP' => $ss > 0 ? (int) round(($ss + $min) / 2) : (int) round($min * 0.6),
                'AMAN' => (int) round(($min + $max) / 2),
                'BERLEBIH' => (int) round($max * 1.6),
                default => (int) round(($min + $max) / 2),
            };
            if ($finalStock < 0) $finalStock = 0;

            // 4. Calculate initial stock = final stock + total consumption
            $initQty = $finalStock + $totalConsumption;

            // 5. Create initial stok_masuk & fifo_batch
            $firstDate = (new \DateTime($startDate))->modify('-1 day')->format('Y-m-d');
            $initBatchKode = 'BATCH-INIT-' . strtoupper(substr(uniqid(), -5));

            $initStokMasuk = StokMasuk::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplierId,
                'jumlah' => $initQty,
                'batch_kode' => $initBatchKode,
                'tanggal_masuk' => $firstDate,
                'user_id' => $this->userId,
                'keterangan' => 'Stok awal ' . $bahan->nama_bahan,
            ]);

            $batch = FifoBatch::create([
                'bahan_baku_id' => $bahan->id,
                'stok_masuk_id' => $initStokMasuk->id,
                'batch_kode' => $initBatchKode,
                'jumlah_awal' => $initQty,
                'sisa_stok' => $initQty,
                'tanggal_masuk' => $firstDate,
            ]);

            // 6. Set stok_saat_ini = initQty (before any consumption)
            BahanBaku::where('id', $bahan->id)->update(['stok_saat_ini' => $initQty]);

            // 7. Create stok_keluar for each day, decrementing batch & stok
            foreach ($dailyConsumptions as $date => $qty) {
                $stokKeluar = StokKeluar::create([
                    'bahan_baku_id' => $bahan->id,
                    'jumlah_keluar' => $qty,
                    'tanggal_keluar' => $date,
                    'user_id' => $this->userId,
                    'keterangan' => 'Produksi menu',
                ]);

                $sisa = $qty;
                if ($sisa > $batch->sisa_stok) $sisa = $batch->sisa_stok;

                StokKeluarDetail::create([
                    'stok_keluar_id' => $stokKeluar->id,
                    'fifo_batch_id' => $batch->id,
                    'jumlah_ambil' => $sisa,
                ]);

                $batch->decrement('sisa_stok', $sisa);
                BahanBaku::where('id', $bahan->id)->decrement('stok_saat_ini', $sisa);
            }

            // 8. Update Min-Max values in DB
            BahanBaku::where('id', $bahan->id)->update([
                'safety_stock' => $ss,
                'stok_minimum' => $min,
                'stok_maksimum' => $max,
            ]);

            // 9. Verify final state
            $actualStock = (float) BahanBaku::where('id', $bahan->id)->value('stok_saat_ini');
            $batchSisa = (float) FifoBatch::where('id', $batch->id)->value('sisa_stok');

            $statusLabel = match ($targetStatus) {
                'KRITIS' => 'KRITIS',
                'SEGERA_ROP' => 'SEGERA ROP',
                'AMAN' => 'AMAN',
                'BERLEBIH' => 'BERLEBIH',
            };

            $this->line("    stok={$actualStock} (target: {$finalStock}) batch_sisa={$batchSisa} ss={$ss} min={$min} max={$max} -> {$statusLabel}" . ($actualStock != $batchSisa ? ' ⚠️ MISMATCH!' : ''));
        }
    }

    private function getProfiles(): array
    {
        return [
            'BB001' => ['supplier' => 1,  'range' => [15, 25], 'freq' => 7, 'target' => 'AMAN'],
            'BB002' => ['supplier' => 2,  'range' => [20, 35], 'freq' => 7, 'target' => 'AMAN'],
            'BB003' => ['supplier' => 11, 'range' => [8, 15],  'freq' => 7, 'target' => 'AMAN'],
            'BB004' => ['supplier' => 5,  'range' => [8, 14],  'freq' => 7, 'target' => 'AMAN'],
            'BB005' => ['supplier' => 5,  'range' => [8, 14],  'freq' => 7, 'target' => 'AMAN'],
            'BB006' => ['supplier' => 4,  'range' => [3, 7],   'freq' => 6, 'target' => 'AMAN'],
            'BB007' => ['supplier' => 9,  'range' => [4, 8],   'freq' => 6, 'target' => 'AMAN'],
            'BB008' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 7, 'target' => 'AMAN'],
            'BB009' => ['supplier' => 7,  'range' => [2, 4],   'freq' => 6, 'target' => 'KRITIS'],
            'BB010' => ['supplier' => 8,  'range' => [3, 6],   'freq' => 5, 'target' => 'AMAN'],
            'BB011' => ['supplier' => 4,  'range' => [2, 5],   'freq' => 5, 'target' => 'SEGERA_ROP'],
            'BB012' => ['supplier' => 9,  'range' => [3, 6],   'freq' => 5, 'target' => 'SEGERA_ROP'],
            'BB013' => ['supplier' => 10, 'range' => [1, 3],   'freq' => 5, 'target' => 'BERLEBIH'],
            'BB014' => ['supplier' => 6,  'range' => [5, 10],  'freq' => 5, 'target' => 'AMAN'],
            'BB015' => ['supplier' => 7,  'range' => [3, 6],   'freq' => 6, 'target' => 'AMAN'],
            'BB016' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 5, 'target' => 'BERLEBIH'],
            'BB017' => ['supplier' => 7,  'range' => [3, 5],   'freq' => 5, 'target' => 'SEGERA_ROP'],
            'BB018' => ['supplier' => 7,  'range' => [2, 4],   'freq' => 7, 'target' => 'BERLEBIH'],
            'BB019' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 5, 'target' => 'AMAN'],
            'BB020' => ['supplier' => 7,  'range' => [1, 3],   'freq' => 6, 'target' => 'KRITIS'],
        ];
    }
}
