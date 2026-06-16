### Blackbox Testing - Transaksi Stok Masuk

**Nama Fitur:** Transaksi Stok Masuk

**Halaman:** Stok Masuk

**Role Pengakses:** Admin, Karyawan

| No | Scenario | Test Data | Test Step | Expected Result | Result | Status |
|:--:|:---------|:----------|:----------|:---------------|:------:|:------:|
| 1 | Menambah stok masuk dengan data valid | Bahan Baku: Tepung Terigu <br>Supplier: PT Sumber Makmur <br>Jumlah: 100 <br>Tanggal: 2026-06-17 | 1. Login ke sistem <br>2. Buka menu Stok Masuk <br>3. Klik "Tambah Stok Masuk" <br>4. Isi seluruh field <br>5. Klik "Simpan" | Data tersimpan. Muncul notifikasi "Stok masuk berhasil dicatat." | Data tersimpan. Muncul notifikasi "Stok masuk berhasil dicatat." | PASS |
| 2 | Verifikasi kode batch otomatis | Batch Kode: (otomatis) | 1. Lakukan skenario no.1 <br>2. Cek tabel fifo_batches di database | Batch terbentuk format BATCH-YYMMDD-XXX, sisa_stok = jumlah_awal | Batch terbentuk format BATCH-260617-XXX, sisa_stok = 100 | PASS |
| 3 | Mencari stok masuk berdasarkan kode batch | Kata kunci: BATCH-2606 | 1. Login <br>2. Buka menu Stok Masuk <br>3. Ketik "BATCH-2606" di pencarian <br>4. Klik "Cari" | Tabel menampilkan data stok masuk yang sesuai dengan kode batch yang dicari | Tabel menampilkan data stok masuk yang sesuai dengan kode batch yang dicari | PASS |
| 4 | Verifikasi penambahan stok bahan baku | Stok awal: 50 <br>Jumlah masuk: 100 <br>Stok akhir: 150 | 1. Lakukan skenario no.1 <br>2. Cek stok_saat_ini di tabel bahan_bakus | stok_saat_ini bahan baku bertambah (50 + 100 = 150) | stok_saat_ini bahan baku menjadi 150 | PASS |
| 5 | Mencari stok masuk berdasarkan nama bahan | Kata kunci: Tepung | 1. Login <br>2. Buka menu Stok Masuk <br>3. Ketik "Tepung" di pencarian <br>4. Klik "Cari" | Tabel menampilkan data stok masuk yang sesuai dengan nama bahan yang dicari | Tabel menampilkan data stok masuk yang sesuai dengan nama bahan yang dicari | PASS |
