<?php

namespace Database\Seeders;

use App\Models\Supplier;
use App\Models\Satuan;
use App\Models\BahanBaku;
use App\Models\StokMasuk;
use App\Models\FifoBatch;
use App\Models\StokKeluar;
use App\Models\StokKeluarDetail;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================================
        // 1. SUPPLIER (10)
        // ============================================================
        $suppliers = Supplier::insert([
            ['nama_supplier' => 'PT Sumber Beras', 'telepon' => '021-5550011', 'alamat' => 'Jl. Raya Cipinang No.1, Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Makmur Ternak', 'telepon' => '021-5550022', 'alamat' => 'Jl. Ternak Ayam No.10, Bogor', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'UD Boga Segar', 'telepon' => '022-5550033', 'alamat' => 'Jl. Sayur Segar No.5, Bandung', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Lautan Cumi', 'telepon' => '031-5550044', 'alamat' => 'Jl. Ikan No.8, Surabaya', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'Tahu Tempe Subur', 'telepon' => '024-5550055', 'alamat' => 'Jl. Tempe No.3, Semarang', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Sapi Perkasa', 'telepon' => '021-5550066', 'alamat' => 'Jl. Daging No.15, Jakarta', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Sayur Organik', 'telepon' => '0251-5550077', 'alamat' => 'Jl. Hidroponik No.7, Bandung', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'UD Bebek Pedaging', 'telepon' => '0271-5550088', 'alamat' => 'Jl. Bebek No.9, Solo', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'PT Ikan Air Tawar', 'telepon' => '061-5550099', 'alamat' => 'Jl. Lele No.12, Medan', 'created_at' => now(), 'updated_at' => now()],
            ['nama_supplier' => 'CV Buah Segar', 'telepon' => '0361-5550100', 'alamat' => 'Jl. Jeruk No.6, Denpasar', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $suppliers = Supplier::all();

        // ============================================================
        // 2. SATUAN (5)
        // ============================================================
        $satuans = Satuan::insert([
            ['nama_satuan' => 'Kg', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Butir', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Ekor', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Buah', 'created_at' => now(), 'updated_at' => now()],
            ['nama_satuan' => 'Ikat', 'created_at' => now(), 'updated_at' => now()],
        ]);

        $satuans = Satuan::all()->keyBy('nama_satuan');

        // ============================================================
        // 3. BAHAN BAKU (20)
        // Format per bahan:
        // [nama, kode, satuan, min, max, lead_time, [
        //   [jumlah, hari_lalu, supplier_index], ... stok_masuk
        // ], [
        //   [jumlah, hari_lalu], ... stok_keluar
        // ]]
        // supplier_index: 0-based index ke $suppliers
        // user bergantian: 1=admin, 2=karyawan
        // ============================================================

        $bahanList = [
            // 1. Beras
            ['Beras', 'BB001', 'Kg', 50, 200, 1, 180, [
                [80, 78, 0], [50, 43, 0], [40, 10, 0],
            ], [
                [22, 73], [18, 45], [15, 24], [10, 3],
            ]],
            // 2. Ayam
            ['Ayam', 'BB003', 'Ekor', 20, 80, 1, 10, [
                [30, 73, 1], [25, 38, 1], [20, 8, 1],
            ], [
                [10, 70], [8, 38], [6, 15], [5, 3],
            ]],
            // 3. Tempe
            ['Tempe', 'BB004', 'Buah', 10, 20, 1, 5, [
                [30, 73, 4], [25, 35, 4], [15, 4, 4],
            ], [
                [10, 70], [8, 35], [5, 15], [5, 1],
            ]],
            // 4. Tahu
            ['Tahu', 'BB005', 'Buah', 30, 100, 1, 5, [
                [40, 70, 1], [30, 35, 1], [20, 4, 1],
            ], [
                [12, 66], [10, 32], [8, 12], [6, 2],
            ]],
            // 5. Udang
            ['Udang', 'BB006', 'Kg', 10, 40, 1, 7, [
                [15, 75, 3], [12, 38, 3], [10, 5, 3],
            ], [
                [8, 70], [7, 35], [6, 10], [5, 1],
            ]],
            // 6. Cabai Merah
            ['Cabai Merah', 'BB008', 'Kg', 15, 50, 1, 7, [
                [14, 73, 2], [10, 38, 2], [8, 5, 2],
            ], [
                [6, 66], [5, 35], [6, 12], [5, 2],
            ]],
            // 7. Bebek
            ['Bebek', 'BB010', 'Ekor', 15, 50, 1, 10, [
                [18, 73, 7], [12, 38, 7], [10, 5, 7],
            ], [
                [9, 70], [8, 35], [6, 15], [5, 2],
            ]],
            // 8. Lele
            ['Lele', 'BB012', 'Ekor', 15, 40, 1, 30, [
                [12, 78, 8], [10, 38, 8], [8, 5, 8],
            ], [
                [7, 73], [6, 35], [5, 10],
            ]],
            // 9. Cabai Rawit
            ['Cabai Rawit', 'BB009', 'Kg', 10, 40, 1, 21, [
                [6, 73, 2], [5, 38, 2], [5, 5, 2],
            ], [
                [5, 66], [4, 35], [3, 12], [2, 1],
            ]],
            // 10. Cumi
            ['Cumi', 'BB011', 'Kg', 10, 30, 1, 30, [
                [12, 78, 3], [10, 38, 3], [8, 5, 3],
            ], [
                [8, 73], [6, 35], [5, 10], [4, 1],
            ]],
        ];

        $batchCounter = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalDetail = 0;
        $supplierArray = $suppliers->values();

        foreach ($bahanList as $bahanItem) {
            if (count($bahanItem) >= 9) {
                [$nama, $kode, $satuanKey, $min, $max, $leadTime, $hariKedaluwarsa, $masuks, $keluars] = $bahanItem;
            } else {
                [$nama, $kode, $satuanKey, $min, $max, $leadTime, $masuks, $keluars] = $bahanItem;
                $hariKedaluwarsa = null;
            }

            $safetyStock = round($min * 0.2, 0);
            $bahan = BahanBaku::create([
                'kode_bahan' => $kode,
                'nama_bahan' => $nama,
                'satuan_id' => $satuans[$satuanKey]->id,
                'stok_saat_ini' => 0,
                'stok_minimum' => $min,
                'stok_maksimum' => $max,
                'safety_stock' => $safetyStock,
                'reorder_point' => $min,
                'lead_time' => $leadTime,
                'hari_kedaluwarsa' => $hariKedaluwarsa,
            ]);

            // === STOK MASUK ===
            $userIdCycle = [1, 2];
            foreach ($masuks as $i => $m) {
                [$jumlah, $hariLalu, $supplierIdx] = $m;
                $batchCounter++;
                $totalMasuk++;
                $tanggal = Carbon::today()->subDays($hariLalu);

                $stokMasuk = StokMasuk::create([
                    'bahan_baku_id' => $bahan->id,
                    'supplier_id' => $supplierArray[$supplierIdx]->id,
                    'jumlah' => $jumlah,
                    'batch_kode' => sprintf('BATCH-%03d', $batchCounter),
                    'tanggal_masuk' => $tanggal,
                    'user_id' => $userIdCycle[$i % 2],
                    'keterangan' => 'Pembelian ' . $nama,
                ]);

                FifoBatch::create([
                    'bahan_baku_id' => $bahan->id,
                    'stok_masuk_id' => $stokMasuk->id,
                    'batch_kode' => sprintf('BATCH-%03d', $batchCounter),
                    'jumlah_awal' => $jumlah,
                    'sisa_stok' => $jumlah,
                    'tanggal_masuk' => $tanggal,
                ]);

                $bahan->increment('stok_saat_ini', $jumlah);
            }

            // === STOK KELUAR (FIFO) ===
            foreach ($keluars as $j => $k) {
                [$jumlahKeluar, $hariLalu] = $k;
                $totalKeluar++;
                $tanggalKeluar = Carbon::today()->subDays($hariLalu);

                $sisaKebutuhan = $jumlahKeluar;
                $batches = FifoBatch::where('bahan_baku_id', $bahan->id)
                    ->where('sisa_stok', '>', 0)
                    ->orderBy('tanggal_masuk')
                    ->orderBy('id')
                    ->get();

                $stokKeluar = StokKeluar::create([
                    'bahan_baku_id' => $bahan->id,
                    'jumlah_keluar' => $jumlahKeluar,
                    'tanggal_keluar' => $tanggalKeluar,
                    'user_id' => $userIdCycle[$j % 2],
                    'keterangan' => 'Produksi menu',
                ]);

                foreach ($batches as $batch) {
                    if ($sisaKebutuhan <= 0) break;
                    $ambil = min($batch->sisa_stok, $sisaKebutuhan);

                    StokKeluarDetail::create([
                        'stok_keluar_id' => $stokKeluar->id,
                        'fifo_batch_id' => $batch->id,
                        'jumlah_ambil' => $ambil,
                    ]);
                    $totalDetail++;

                    $batch->decrement('sisa_stok', $ambil);
                    $sisaKebutuhan -= $ambil;
                }

                $bahan->decrement('stok_saat_ini', $jumlahKeluar);
            }
        }

        // ============================================================
        // Post-process: update min/max/safety from actual consumption
        // ============================================================
        foreach (BahanBaku::all() as $b) {
            $daily = StokKeluar::where('bahan_baku_id', $b->id)
                ->get()
                ->groupBy(fn($sk) => $sk->tanggal_keluar->format('Y-m-d'))
                ->map(fn($items) => $items->sum('jumlah_keluar'));

            if ($daily->isEmpty()) continue;

            $rmax = $daily->max();
            $rata = $daily->avg();
            $lt = max((int)($b->lead_time ?? 1), 1);

            $safetyStock = round(($rmax - $rata) * $lt, 2);
            $stokMin     = round($rmax * $lt, 2);
            $stokMax     = round($stokMin + $safetyStock, 2);

            $b->update([
                'safety_stock'  => max($safetyStock, 0),
                'stok_minimum'  => $stokMin,
                'stok_maksimum' => $stokMax,
                'reorder_point' => $stokMin,
            ]);
        }
    }
}
