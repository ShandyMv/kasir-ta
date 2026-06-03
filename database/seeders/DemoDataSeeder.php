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
        // [nama, kode, satuan, min, max, [
        //   [jumlah, hari_lalu, supplier_index], ... stok_masuk (50 total)
        // ], [
        //   [jumlah, hari_lalu], ... stok_keluar (30 total)
        // ]]
        // supplier_index: 0-based index ke $suppliers
        // user bergantian: 1=admin, 2=karyawan
        // ============================================================

        $bahanList = [
            // ===================== AMAN (7) =====================
            // 1. Beras — 3 masuk, 2 keluar
            ['Beras', 'BB001', 'Kg', 50, 200, [
                [60, 48, 0], [40, 22, 0], [30, 5, 0],
            ], [
                [35, 10], [25, 3],
            ]],
            // 2. Telur — 3 masuk, 2 keluar
            ['Telur', 'BB002', 'Butir', 100, 400, [
                [120, 50, 1], [100, 24, 1], [60, 7, 1],
            ], [
                [70, 12], [60, 4],
            ]],
            // 3. Ayam — 3 masuk, 2 keluar
            ['Ayam', 'BB003', 'Ekor', 20, 80, [
                [30, 46, 1], [20, 20, 1], [15, 4, 1],
            ], [
                [12, 8], [8, 3],
            ]],
            // 4. Tempe — 3 masuk, 2 keluar
            ['Tempe', 'BB004', 'Buah', 20, 60, [
                [25, 44, 4], [20, 18, 4], [15, 6, 4],
            ], [
                [10, 9], [8, 2],
            ]],
            // 5. Tahu — 3 masuk, 2 keluar
            ['Tahu', 'BB005', 'Buah', 30, 100, [
                [35, 42, 4], [25, 16, 4], [18, 8, 4],
            ], [
                [12, 7], [10, 2],
            ]],
            // 6. Udang — 2 masuk, 2 keluar
            ['Udang', 'BB006', 'Kg', 10, 40, [
                [15, 52, 3], [12, 22, 3],
            ], [
                [8, 6], [7, 2],
            ]],
            // 7. Ikan Kembung — 2 masuk, 1 keluar
            ['Ikan Kembung', 'BB007', 'Ekor', 10, 40, [
                [12, 47, 8], [10, 21, 8],
            ], [
                [7, 5],
            ]],

            // ===================== RESTOCK (6) =====================
            // 8. Cabai Merah — 3 masuk, 2 keluar
            ['Cabai Merah', 'BB008', 'Kg', 15, 50, [
                [7, 55, 2], [6, 26, 2], [4, 10, 2],
            ], [
                [7, 6], [7, 2],
            ]],
            // 9. Cabai Rawit — 3 masuk, 2 keluar
            ['Cabai Rawit', 'BB009', 'Kg', 10, 40, [
                [5, 53, 2], [4, 24, 2], [3, 12, 2],
            ], [
                [5, 8], [5, 3],
            ]],
            // 10. Bebek — 3 masuk, 2 keluar
            ['Bebek', 'BB010', 'Ekor', 15, 50, [
                [9, 54, 7], [7, 28, 7], [5, 14, 7],
            ], [
                [9, 10], [7, 4],
            ]],
            // 11. Cumi — 2 masuk, 2 keluar
            ['Cumi', 'BB011', 'Kg', 10, 30, [
                [6, 56, 3], [4, 28, 3],
            ], [
                [4, 7], [4, 2],
            ]],
            // 12. Lele — 2 masuk, 1 keluar
            ['Lele', 'BB012', 'Ekor', 15, 40, [
                [8, 45, 8], [7, 19, 8],
            ], [
                [11, 9],
            ]],
            // 13. Jeruk Nipis — 2 masuk, 1 keluar
            ['Jeruk Nipis', 'BB013', 'Kg', 5, 20, [
                [4, 57, 9], [3, 29, 9],
            ], [
                [6, 8],
            ]],

            // ===================== BERLEBIH (7) =====================
            // 14. Daging Sapi — 3 masuk, 1 keluar
            ['Daging Sapi', 'BB014', 'Kg', 10, 30, [
                [22, 44, 5], [18, 17, 5], [12, 2, 5],
            ], [
                [5, 9],
            ]],
            // 15. Kangkung — 3 masuk, 1 keluar
            ['Kangkung', 'BB015', 'Ikat', 5, 20, [
                [14, 40, 6], [10, 15, 6], [8, 1, 6],
            ], [
                [4, 6],
            ]],
            // 16. Bayam — 2 masuk, 1 keluar
            ['Bayam', 'BB016', 'Ikat', 5, 15, [
                [12, 38, 6], [10, 14, 6],
            ], [
                [2, 7],
            ]],
            // 17. Kol — 2 masuk, 1 keluar
            ['Kol', 'BB017', 'Kg', 10, 25, [
                [18, 36, 2], [14, 12, 2],
            ], [
                [3, 8],
            ]],
            // 18. Kemangi — 2 masuk, 1 keluar
            ['Kemangi', 'BB018', 'Ikat', 5, 15, [
                [12, 43, 6], [10, 15, 6],
            ], [
                [3, 7],
            ]],
            // 19. Kacang Tanah — 2 masuk, 1 keluar
            ['Kacang Tanah', 'BB019', 'Kg', 10, 30, [
                [22, 35, 9], [18, 11, 9],
            ], [
                [4, 9],
            ]],
            // 20. Daun Jeruk — 2 masuk, 1 keluar
            ['Daun Jeruk', 'BB020', 'Ikat', 3, 10, [
                [10, 39, 6], [8, 13, 6],
            ], [
                [3, 10],
            ]],
        ];

        $batchCounter = 0;
        $totalMasuk = 0;
        $totalKeluar = 0;
        $totalDetail = 0;
        $supplierArray = $suppliers->values();

        foreach ($bahanList as $bahanItem) {
            [$nama, $kode, $satuanKey, $min, $max, $masuks, $keluars] = $bahanItem;

            $bahan = BahanBaku::create([
                'kode_bahan' => $kode,
                'nama_bahan' => $nama,
                'satuan_id' => $satuans[$satuanKey]->id,
                'stok_saat_ini' => 0,
                'stok_minimum' => $min,
                'stok_maksimum' => $max,
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
    }
}
