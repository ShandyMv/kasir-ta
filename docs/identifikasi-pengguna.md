# Identifikasi Pengguna, Data, dan Kebutuhan Sistem SuperStock

---

## 1. Identifikasi Pengguna

Identifikasi pengguna dilakukan untuk mengetahui siapa saja yang akan menggunakan sistem aplikasi pengelolaan stok **SuperStock**. Pengguna dikelompokkan berdasarkan peran (role) yang memiliki hak akses berbeda terhadap fitur dan data dalam sistem.

### 1.1 Sistem Autentikasi

- Login menggunakan **username** (bukan email)
- Metode autentikasi: **session-based** (guard `web`, driver `session`)
- Logout via `POST /logout`
- Rate limiting login menggunakan `throttle:6,1`

### 1.2 Tabel Identifikasi Pengguna

| No | Role | Deskripsi |
|:--:|:----|:----------|
| 1 | **Admin** | Pengguna dengan akses penuh terhadap seluruh fitur sistem, termasuk manajemen data master, transaksi stok, monitoring, dan laporan |
| 2 | **Karyawan** | Pengguna dengan akses terbatas pada pencatatan transaksi stok masuk dan stok keluar serta monitoring FIFO |
| 3 | **Owner** | Pengguna dengan akses read-only untuk melihat monitoring, analisis, dan laporan tanpa dapat melakukan transaksi atau mengelola data master |

### 1.3 Struktur Data User

| Field | Tipe Data | Keterangan |
|-------|-----------|------------|
| `id` | bigInteger, auto increment | Primary key |
| `name` | string | Nama lengkap pengguna |
| `username` | string, unique | Username untuk login |
| `email` | string, unique | Alamat email |
| `password` | string, hashed | Password terenkripsi |
| `role` | enum ('admin', 'karyawan', 'owner') | Role pengguna |
| `remember_token` | string, nullable | Token sesi |
| `email_verified_at` | timestamp, nullable | Waktu verifikasi email |
| `created_at` | timestamp | Waktu data dibuat |
| `updated_at` | timestamp | Waktu data diupdate |

---

## 2. Peran dan Tanggung Jawab

### 2.1 Admin

| No | Peran | Tanggung Jawab |
|:--:|:-----|:---------------|
| 1 | Manajemen Pengguna | Melakukan CRUD data pengguna (Admin, Karyawan, Owner) termasuk mengatur role dan hak akses |
| 2 | Manajemen Supplier | Melakukan CRUD data supplier/pemasok bahan baku |
| 3 | Manajemen Satuan | Melakukan CRUD data satuan unit pengukuran bahan baku |
| 4 | Manajemen Bahan Baku | Melakukan CRUD data bahan baku beserta stok minimum dan maksimum |
| 5 | Transaksi Stok Masuk | Mencatat penerimaan barang dari supplier ke gudang |
| 6 | Transaksi Stok Keluar | Mencatat pengeluaran barang dari gudang berdasarkan metode FIFO |
| 7 | Monitoring FIFO | Memantau usia batch FIFO untuk prioritas penggunaan stok |
| 8 | Analisis Min-Max | Menganalisis kesehatan stok (aman, restock, berlebih) |
| 9 | Laporan & Ekspor | Melihat dan mengekspor laporan stok masuk, stok keluar, dan persediaan ke PDF/Excel |

### 2.2 Karyawan

| No | Peran | Tanggung Jawab |
|:--:|:-----|:---------------|
| 1 | Transaksi Stok Masuk | Mencatat penerimaan barang dari supplier ke gudang |
| 2 | Transaksi Stok Keluar | Mencatat pengeluaran barang dari gudang berdasarkan metode FIFO |
| 3 | Monitoring FIFO | Memantau usia batch FIFO untuk prioritas penggunaan stok |

### 2.3 Owner

| No | Peran | Tanggung Jawab |
|:--:|:-----|:---------------|
| 1 | Monitoring FIFO | Memantau usia batch FIFO dan ringkasan kesehatan batch |
| 2 | Analisis Min-Max | Menganalisis status stok bahan baku (aman, restock, berlebih) |
| 3 | Laporan & Ekspor | Melihat dan mengekspor laporan stok masuk, stok keluar, dan persediaan ke PDF/Excel |

### 2.4 Matriks Fitur per Role

| Fitur | Admin | Karyawan | Owner |
|-------|:-----:|:--------:|:-----:|
| Dashboard | ✅ | ✅ | ✅ |
| Manajemen User | ✅ | ❌ | ❌ |
| Manajemen Supplier | ✅ | ❌ | ❌ |
| Manajemen Satuan | ✅ | ❌ | ❌ |
| Manajemen Bahan Baku | ✅ | ❌ | ❌ |
| Transaksi Stok Masuk | ✅ | ✅ | ❌ |
| Transaksi Stok Keluar (FIFO) | ✅ | ✅ | ❌ |
| Monitoring FIFO | ✅ | ✅ | ✅ |
| Analisis Min-Max | ✅ | ❌ | ✅ |
| Laporan & Export (PDF/Excel) | ✅ | ❌ | ✅ |
| Edit Profil | ✅ | ✅ | ✅ |

---

## 3. Identifikasi Data

Identifikasi data dilakukan untuk mengetahui data-data yang dibutuhkan dalam proses pengelolaan stok di aplikasi **SuperStock**. Data yang diidentifikasi akan digunakan sebagai dasar dalam perancangan database serta pengembangan fitur pada aplikasi yang akan dibangun. Dengan adanya identifikasi data, sistem diharapkan mampu menyediakan informasi yang akurat, terstruktur, dan sesuai dengan kebutuhan pengguna dalam mendukung proses pengelolaan stok.

### Tabel 3.3 Tabel Identifikasi Data

| Data Master | Data Transaksi |
|:-----------|:--------------|
| 1. Data Admin | 1. Data Transaksi Penerimaan Stok |
| 2. Data Karyawan | 2. Data Transaksi Pengeluaran Stok |
| 3. Data Owner | 3. Data Monitoring & Analisis Stok |
| 4. Data Supplier | 4. Data Batch FIFO |
| 5. Data Satuan Stok | 5. Laporan Persediaan Stok |
| 6. Data Nama Stok (Bahan Baku) | 6. Data Monitoring Stok (FIFO & Min-Max) |

### 3.1 Penjelasan Data Master

| No | Data Master | Entitas | Deskripsi |
|:--:|:-----------|:--------|:----------|
| 1 | Data Admin | `users` (role=admin) | Data pengguna dengan role admin yang memiliki akses penuh terhadap seluruh fitur sistem |
| 2 | Data Karyawan | `users` (role=karyawan) | Data pengguna dengan role karyawan yang bertugas mencatat transaksi stok masuk dan keluar |
| 3 | Data Owner | `users` (role=owner) | Data pengguna dengan role owner yang memantau monitoring dan laporan stok |
| 4 | Data Supplier | `suppliers` | Data pemasok/vendor bahan baku yang memasok barang ke gudang |
| 5 | Data Satuan Stok | `satuans` | Data satuan unit pengukuran yang digunakan pada bahan baku (kg, liter, pcs, dll) |
| 6 | Data Nama Stok (Bahan Baku) | `bahan_bakus` | Data bahan baku yang menjadi objek pengelolaan stok, dilengkapi kode bahan, stok minimum, dan stok maksimum |

### 3.2 Penjelasan Data Transaksi

| No | Data Transaksi | Entitas | Deskripsi | Pengguna Terkait |
|:--:|:--------------|:--------|:----------|:----------------:|
| 1 | Data Transaksi Penerimaan Stok | `stok_masuks` | Data penerimaan barang dari supplier yang dicatat oleh user, mencakup jumlah, batch kode, dan tanggal masuk | Admin, Karyawan |
| 2 | Data Transaksi Pengeluaran Stok | `stok_keluars`, `stok_keluar_details` | Data pengeluaran barang untuk produksi yang dicatat dengan metode FIFO, mencakup jumlah keluar dan alokasi batch | Admin, Karyawan |
| 3 | Data Monitoring & Analisis Stok | — | Data hasil monitoring dan analisis stok yang ditampilkan di dashboard owner (grafik, ringkasan FIFO, analisis Min-Max) | Owner |
| 4 | Data Batch FIFO | `fifo_batches` | Data batch FIFO yang dibuat otomatis saat penerimaan stok, mencatat jumlah awal, sisa stok, dan tanggal masuk untuk pelacakan usia stok | Admin, Karyawan, Owner |
| 5 | Laporan Persediaan Stok | — | Laporan yang menampilkan data persediaan bahan baku terkini beserta status stok (aman, restock, berlebih) yang dapat diekspor ke PDF/Excel | Admin, Owner |
| 6 | Data Monitoring Stok (FIFO & Min-Max) | — | Data monitoring yang menampilkan informasi aging batch FIFO dan analisis Min-Max untuk pengambilan keputusan | Admin, Owner |

---

## 4. Kebutuhan Pengguna

Identifikasi kebutuhan pengguna dilakukan untuk mengetahui data, informasi, dan laporan yang diperlukan oleh setiap pengguna dalam menjalankan peran dan tanggung jawabnya pada sistem pengelolaan stok **SuperStock**.

| No | Pengguna | Kebutuhan Data | Kebutuhan Informasi | Kebutuhan Laporan |
|:--:|:--------|:---------------|:-------------------|:-----------------|
| 1 | Admin | Data user, supplier, satuan, bahan baku, stok masuk, stok keluar, batch FIFO | Ringkasan dashboard (total bahan baku, supplier, user, stok menipis), notifikasi stok kritis, grafik perbandingan stok, aktivitas terbaru | Laporan stok masuk, laporan stok keluar, laporan persediaan (PDF/Excel) |
| 2 | Karyawan | Data stok masuk, stok keluar, batch FIFO, bahan baku | Prioritas FIFO hari ini, aktivitas stok masuk & keluar hari ini, daftar batch yang perlu segera digunakan | — |
| 3 | Owner | Data monitoring FIFO, analisis Min-Max, laporan stok | Kesehatan stok (aman, restock, berlebih), ringkasan FIFO (batch tertua, terbanyak, kritis), grafik tren persediaan bulanan, rekomendasi restock | Laporan stok masuk, laporan stok keluar, laporan persediaan (PDF/Excel) |

---

## 5. Kebutuhan Fungsional

Identifikasi kebutuhan fungsional dilakukan untuk mengetahui fitur-fitur yang harus tersedia dalam sistem berdasarkan peran masing-masing pengguna.

| No | Pengguna | Kebutuhan Fungsional |
|:--:|:--------|:---------------------|
| 1 | Admin | 1. Login menggunakan username dan password |
| | | 2. Melihat dashboard admin yang berisi ringkasan sistem |
| | | 3. Mengelola data user (CRUD) — tambah, lihat, edit, hapus pengguna |
| | | 4. Mengelola data supplier (CRUD) — tambah, lihat, edit, hapus supplier |
| | | 5. Mengelola data satuan (CRUD) — tambah, lihat, edit, hapus satuan |
| | | 6. Mengelola data bahan baku (CRUD) — tambah, lihat, edit, hapus bahan baku |
| | | 7. Mencatat transaksi penerimaan stok |
| | | 8. Mencatat transaksi pengeluaran stok dengan metode FIFO |
| | | 9. Melihat monitoring batch FIFO |
| | | 10. Melihat analisis Min-Max stok |
| | | 11. Melihat dan mengekspor laporan (PDF/Excel) — stok masuk, stok keluar, persediaan |
| | | 12. Mengedit profil sendiri (nama, email) |
| | | 13. Melakukan logout |
| 2 | Karyawan | 1. Login menggunakan username dan password |
| | | 2. Melihat dashboard karyawan yang berisi aktivitas hari ini dan prioritas FIFO |
| | | 3. Mencatat transaksi penerimaan stok |
| | | 4. Mencatat transaksi pengeluaran stok dengan metode FIFO |
| | | 5. Melihat monitoring batch FIFO |
| | | 6. Mengedit profil sendiri (nama, email) |
| | | 7. Melakukan logout |
| 3 | Owner | 1. Login menggunakan username dan password |
| | | 2. Melihat dashboard owner yang berisi ringkasan kesehatan stok, grafik persediaan, ringkasan FIFO, dan ringkasan Min-Max |
| | | 3. Melihat monitoring batch FIFO |
| | | 4. Melihat analisis Min-Max stok |
| | | 5. Melihat dan mengekspor laporan (PDF/Excel) — stok masuk, stok keluar, persediaan |
| | | 6. Mengedit profil sendiri (nama, email) |
| | | 7. Melakukan logout |

---

## 6. Kebutuhan Non-Fungsional

Identifikasi kebutuhan non-fungsional dilakukan untuk mengetahui kualitas, batasan, dan karakteristik yang harus dimiliki oleh sistem aplikasi pengelolaan stok **SuperStock**.

| No | Non Fungsional | Kebutuhan | Lampiran |
|:--:|:--------------|:----------|:---------|
| 1 | **Keamanan** | Autentikasi menggunakan session-based dengan password terenkripsi (bcrypt/hashed), rate limiting untuk mencegah brute force login, otorisasi berbasis role menggunakan middleware untuk membatasi akses pengguna, validasi input pada setiap form untuk mencegah serangan injeksi | `config/auth.php`, `app/Http/Middleware/RoleMiddleware.php`, `app/Http/Requests/` |
| 2 | **Kinerja** | Pagination data (10-20 data per halaman), indexing database pada kolom yang sering diquery, eager loading untuk mengoptimalkan query relasi, caching session pada database | Migrasi `fifo_batches` index `[bahan_baku_id, tanggal_masuk]`, `stok_keluar_details` index `[stok_keluar_id, fifo_batch_id]` |
| 3 | **Keandalan** | Soft deletes pada data master (bahan baku, supplier, satuan) untuk mencegah kehilangan data, foreign key constraints dengan `restrict` pada data yang memiliki relasi transaksi, database transaction pada proses FIFO untuk menjaga konsistensi data | Migrasi `softDeletes()`, `onDelete('restrict')`, `DB::transaction()` di `FIFOService.php` |
| 4 | **Usability** | Tampilan sidebar dan dashboard yang menyesuaikan role pengguna, fitur pencarian dan filter pada setiap halaman data, badge status visual dengan kode warna (denger untuk kritis, warning, success), navigasi yang konsisten dan responsif | `resources/views/layouts/partials/sidebar.blade.php`, `resources/views/layouts/navigation.blade.php` |
| 5 | **Portabilitas** | Ekspor laporan ke format PDF (menggunakan DomPDF dengan dukungan UTF-8 font DejaVu Sans) dan Excel (menggunakan Maatwebsite Laravel-Excel), tampilan cetak yang rapi dengan format tabel dan header | `app/Http/Controllers/LaporanController.php`, `resources/views/laporan/pdf.blade.php`, `app/Exports/LaporanExport.php` |
| 6 | **Maintainability** | Pemisahan logika bisnis ke service layer (FIFOService, MinMaxAnalysisService), validasi request menggunakan Form Request terpisah, penggunaan model Eloquent dengan relasi yang terdefinisi dengan baik, kode mengikuti standar PSR-4 dan fitur PHP 8.2+ | `app/Services/FIFOService.php`, `app/Services/MinMaxAnalysisService.php`, `app/Http/Requests/` |
