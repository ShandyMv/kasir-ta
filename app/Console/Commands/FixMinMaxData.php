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
            $expiry = $p['expiry'] ?? 30;

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

            $totalNeeded = $totalConsumption + $finalStock;

            // 4. Determine batch schedule based on expiry
            $batchDates = $p['batches'] ?? $this->getBatchDates($expiry);

            // 5. Create batches with stock distribution
            $totalStockNeeded = 0;
            $createdBatches = [];
            foreach ($batchDates as $bd) {
                $qty = (int) round($totalNeeded * $bd['proporsi']);
                if ($qty < 1) $qty = 1;
                $totalStockNeeded += $qty;
            }

            // Adjust last batch to match totalNeeded
            $lastIdx = count($batchDates) - 1;
            $diff = $totalNeeded - $totalStockNeeded;
            $batchDates[$lastIdx]['proporsi'] = ($batchDates[$lastIdx]['proporsi'] * $totalNeeded + $diff) / $totalNeeded;

            $cumStock = 0;
            foreach ($batchDates as $i => $bd) {
                $qty = $i === $lastIdx ? $totalNeeded - $cumStock : (int) round($totalNeeded * $bd['proporsi']);
                if ($qty < 1) $qty = 1;
                $cumStock += $qty;

                $sumQty = $totalNeeded;
                $batchKode = 'BATCH-' . strtoupper(substr(uniqid(), -5));

                $stokMasuk = StokMasuk::create([
                    'bahan_baku_id' => $bahan->id,
                    'supplier_id' => $supplierId,
                    'jumlah' => $qty,
                    'batch_kode' => $batchKode,
                    'tanggal_masuk' => $bd['date'],
                    'user_id' => $this->userId,
                    'keterangan' => 'Penerimaan ' . $bahan->nama_bahan . ' (' . $bd['date'] . ')',
                ]);

                $batch = FifoBatch::create([
                    'bahan_baku_id' => $bahan->id,
                    'stok_masuk_id' => $stokMasuk->id,
                    'batch_kode' => $batchKode,
                    'jumlah_awal' => $qty,
                    'sisa_stok' => $qty,
                    'tanggal_masuk' => $bd['date'],
                ]);

                $createdBatches[] = $batch;
            }

            // 6. Set stok_saat_ini = total stock
            BahanBaku::where('id', $bahan->id)->update(['stok_saat_ini' => $totalNeeded]);

            // 7. Consume stock per day using FIFO across batches
            foreach ($dailyConsumptions as $date => $qty) {
                $stokKeluar = StokKeluar::create([
                    'bahan_baku_id' => $bahan->id,
                    'jumlah_keluar' => $qty,
                    'tanggal_keluar' => $date,
                    'user_id' => $this->userId,
                    'keterangan' => 'Produksi menu',
                ]);

                $sisaKebutuhan = $qty;
                foreach ($createdBatches as $cb) {
                    if ($sisaKebutuhan <= 0) break;
                    if ($cb->sisa_stok <= 0) continue;

                    $ambil = min($cb->sisa_stok, $sisaKebutuhan);
                    StokKeluarDetail::create([
                        'stok_keluar_id' => $stokKeluar->id,
                        'fifo_batch_id' => $cb->id,
                        'jumlah_ambil' => $ambil,
                    ]);
                    $cb->decrement('sisa_stok', $ambil);
                    BahanBaku::where('id', $bahan->id)->decrement('stok_saat_ini', $ambil);
                    $sisaKebutuhan -= $ambil;
                }
            }

            // 8. Update Min-Max + expiry values in DB
            BahanBaku::where('id', $bahan->id)->update([
                'safety_stock' => $ss,
                'stok_minimum' => $min,
                'stok_maksimum' => $max,
                'hari_kedaluwarsa' => $expiry,
            ]);

            // 9. Verify final state
            $actualStock = (float) BahanBaku::where('id', $bahan->id)->value('stok_saat_ini');
            $totalSisaBatch = (float) FifoBatch::where('bahan_baku_id', $bahan->id)->sum('sisa_stok');

            $statusLabel = match ($targetStatus) {
                'KRITIS' => 'KRITIS',
                'SEGERA_ROP' => 'SEGERA ROP',
                'AMAN' => 'AMAN',
                'BERLEBIH' => 'BERLEBIH',
            };

            $mismatch = abs($actualStock - $totalSisaBatch) > 0.01;
            $this->line("    stok={$actualStock} (target: {$finalStock}) batch_sisa={$totalSisaBatch} ss={$ss} min={$min} max={$max} -> {$statusLabel} batches=" . count($createdBatches) . ($mismatch ? ' ⚠️ MISMATCH!' : ''));
        }

        // 10. Inject extra batches for FIFO indicator variety
        $this->injectExtraBatches();
    }

    private function injectExtraBatches(): void
    {
        $this->line('');
        $this->line('>>> Menambahkan batch tambahan untuk variasi indikator FIFO...');

        $extra = [
            ['BB008', '2026-06-06', 5, 'KRITIS'],
            ['BB004', '2026-06-10', 15, 'EXPIRED'],
        ];

        foreach ($extra as $e) {
            $bahan = BahanBaku::where('kode_bahan', $e[0])->first();
            if (!$bahan) continue;

            $supplierId = $this->getSupplierForBahan($e[0]);
            $qty = $e[2];
            $tgl = $e[1];
            $indikator = $e[3];

            // Ambil qty dari batch utama (June 17) agar total stok tidak berubah
            $mainBatch = FifoBatch::where('bahan_baku_id', $bahan->id)
                ->where('tanggal_masuk', '2026-06-17')
                ->first();

            if (!$mainBatch || $mainBatch->sisa_stok < $qty) {
                $sisa = $mainBatch ? $mainBatch->sisa_stok : 0;
                $this->warn("  {$e[0]}: skip, batch utama tidak cukup stok (sisa={$sisa})");
                continue;
            }

            // Kurangi batch utama
            $mainBatch->decrement('sisa_stok', $qty);

            // Buat batch extra
            $batchKode = 'BATCH-' . strtoupper(substr(uniqid(), -5));

            StokMasuk::create([
                'bahan_baku_id' => $bahan->id,
                'supplier_id' => $supplierId,
                'jumlah' => $qty,
                'batch_kode' => $batchKode,
                'tanggal_masuk' => $tgl,
                'user_id' => $this->userId,
                'keterangan' => 'Batch tambahan ' . $bahan->nama_bahan . ' (' . $indikator . ')',
            ]);

            FifoBatch::create([
                'bahan_baku_id' => $bahan->id,
                'stok_masuk_id' => StokMasuk::max('id'),
                'batch_kode' => $batchKode,
                'jumlah_awal' => $qty,
                'sisa_stok' => $qty,
                'tanggal_masuk' => $tgl,
                'keterangan' => 'Batch tambahan ' . $indikator,
            ]);

            // stok_saat_ini tidak berubah (transfer dari batch utama ke batch extra)
            $this->line("  {$e[0]} {$bahan->nama_bahan}: ambil {$qty} dari batch 2026-06-17 -> batch {$tgl} ({$indikator})");
        }
    }

    private function getSupplierForBahan(string $kode): int
    {
        $profiles = $this->getProfiles();
        return $profiles[$kode]['supplier'] ?? 1;
    }

    private function getBatchDates(int $expiry): array
    {
        if ($expiry <= 3) {
            return [
                ['date' => '2026-06-17', 'proporsi' => 1.00],
            ];
        } else {
            return [
                ['date' => '2026-06-17', 'proporsi' => 1.00],
            ];
        }
    }

    private function getProfiles(): array
    {
        return [
            'BB001' => ['supplier' => 1,  'range' => [15, 25], 'freq' => 7, 'target' => 'AMAN', 'expiry' => 90],
            'BB002' => ['supplier' => 2,  'range' => [20, 35], 'freq' => 7, 'target' => 'AMAN', 'expiry' => 21],
            'BB003' => ['supplier' => 11, 'range' => [8, 15],  'freq' => 7, 'target' => 'AMAN', 'expiry' => 5],
            'BB004' => ['supplier' => 5,  'range' => [8, 14],  'freq' => 7, 'target' => 'AMAN', 'expiry' => 3],
            'BB005' => ['supplier' => 5,  'range' => [8, 14],  'freq' => 7, 'target' => 'AMAN', 'expiry' => 3],
            'BB006' => ['supplier' => 4,  'range' => [3, 7],   'freq' => 6, 'target' => 'AMAN', 'expiry' => 5],
            'BB007' => ['supplier' => 9,  'range' => [4, 8],   'freq' => 6, 'target' => 'AMAN', 'expiry' => 5],
            'BB008' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 7, 'target' => 'AMAN', 'expiry' => 14],
            'BB009' => ['supplier' => 7,  'range' => [2, 4],   'freq' => 6, 'target' => 'KRITIS', 'expiry' => 14],
            'BB010' => ['supplier' => 8,  'range' => [3, 6],   'freq' => 5, 'target' => 'AMAN', 'expiry' => 14],
            'BB011' => ['supplier' => 4,  'range' => [2, 5],   'freq' => 5, 'target' => 'SEGERA_ROP', 'expiry' => 5],
            'BB012' => ['supplier' => 9,  'range' => [3, 6],   'freq' => 5, 'target' => 'SEGERA_ROP', 'expiry' => 7],
            'BB013' => ['supplier' => 10, 'range' => [1, 3],   'freq' => 5, 'target' => 'BERLEBIH', 'expiry' => 14],
            'BB014' => ['supplier' => 6,  'range' => [5, 10],  'freq' => 5, 'target' => 'AMAN', 'expiry' => 5],
            'BB015' => ['supplier' => 7,  'range' => [3, 6],   'freq' => 6, 'target' => 'AMAN', 'expiry' => 3],
            'BB016' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 5, 'target' => 'BERLEBIH', 'expiry' => 3],
            'BB017' => ['supplier' => 7,  'range' => [3, 5],   'freq' => 5, 'target' => 'SEGERA_ROP', 'expiry' => 7],
            'BB018' => ['supplier' => 7,  'range' => [2, 4],   'freq' => 7, 'target' => 'BERLEBIH', 'expiry' => 3],
            'BB019' => ['supplier' => 7,  'range' => [2, 5],   'freq' => 5, 'target' => 'AMAN', 'expiry' => 90],
            'BB020' => ['supplier' => 7,  'range' => [1, 3],   'freq' => 6, 'target' => 'KRITIS', 'expiry' => 7],
        ];
    }
}
