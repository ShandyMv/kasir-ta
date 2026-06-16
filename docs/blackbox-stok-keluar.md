### Blackbox Testing - Transaksi Stok Keluar

**Nama Fitur:** Transaksi Stok Keluar (FIFO)

**Halaman:** Stok Keluar

**Role Pengakses:** Admin, Karyawan

| No | Scenario | Test Data | Test Step | Expected Result | Result | Status |
|:--:|:---------|:----------|:----------|:---------------|:------:|:------:|
| 1 | Menambah stok keluar dengan data valid | Bahan Baku: Tepung Terigu <br>Batch tersedia: BATCH-260101 (sisa 50), BATCH-260615 (sisa 80) <br>Jumlah keluar: 30 <br>Tanggal: 2026-06-17 | 1. Login ke sistem <br>2. Buka menu Stok Keluar <br>3. Klik "Input Stok Keluar" <br>4. Pilih bahan baku Tepung Terigu <br>5. Isi jumlah 30, pilih tanggal <br>6. Klik "Simpan Transaksi" | Data tersimpan. Muncul notifikasi "Stok keluar berhasil dicatat." | Data tersimpan. Muncul notifikasi "Stok keluar berhasil dicatat." | PASS |
| 2 | Verifikasi FIFO: stok diambil dari batch tertua | Batch A: BATCH-260101 (sisa 50) <br>Batch B: BATCH-260615 (sisa 80) <br>Jumlah keluar: 30 | 1. Lakukan skenario no.1 <br>2. Cek tabel stok_keluar_details <br>3. Cek sisa_stok Batch A di fifo_batches | Stok diambil dari Batch A (tertua). Sisa Batch A = 50 - 30 = 20 | Stok diambil dari Batch A. Sisa Batch A = 20 | PASS |
| 3 | Menambah stok keluar melebihi stok tersedia | Bahan Baku: Tepung Terigu <br>Total sisa batch: 70 <br>Jumlah keluar: 100 | 1. Login <br>2. Buka menu Stok Keluar <br>3. Klik "Input Stok Keluar" <br>4. Pilih bahan baku Tepung Terigu <br>5. Isi jumlah 100 (melebihi stok) <br>6. Klik "Simpan Transaksi" | Muncul error "Stok tersedia tidak mencukupi." | Muncul error "Stok tersedia tidak mencukupi." | PASS |
| 4 | Mencari stok keluar berdasarkan nama bahan | Kata kunci: Tepung | 1. Login <br>2. Buka menu Stok Keluar <br>3. Ketik "Tepung" di kolom pencarian <br>4. Klik "Cari" | Tabel menampilkan data stok keluar yang sesuai dengan nama bahan yang dicari | Tabel menampilkan data stok keluar yang sesuai dengan nama bahan yang dicari | PASS |
| 5 | Verifikasi pengurangan stok bahan baku | Stok awal Tepung: 130 <br>Jumlah keluar: 30 <br>Stok akhir: 100 | 1. Lakukan skenario no.1 <br>2. Cek stok_saat_ini di tabel bahan_bakus | stok_saat_ini bahan baku berkurang (130 - 30 = 100) | stok_saat_ini bahan baku menjadi 100 | PASS |
