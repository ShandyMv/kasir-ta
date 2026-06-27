# Penjelasan Perbedaan Rumus Min-Max di Laporan TA dan Aplikasi

## Daftar Isi

1. [Rumus di Dokumen TA](#rumus-di-dokumen-ta)
2. [Rumus di Aplikasi](#rumus-di-aplikasi)
3. [Kenapa Bisa Berbeda Tapi Hasilnya Sama?](#kenapa-bisa-berbeda-tapi-hasilnya-sama)
4. [Pembuktian Matematis](#pembuktian-matematis)
   - [Safety Stock](#1-safety-stock)
   - [Minimum Stock](#2-minimum-stock)
   - [Maximum Stock](#3-maximum-stock)
   - [Order Quantity](#4-order-quantity)
5. [Tabel Pembuktian dengan Data Real (LT = 1)](#tabel-pembuktian-dengan-data-real-lt--1)
6. [Pembuktian dengan Lead Time Berbeda (LT = 2)](#pembuktian-dengan-lead-time-berbeda-lt--2)
7. [Kesimpulan](#kesimpulan)

---

## Rumus di Dokumen TA

| Komponen | Rumus | Keterangan |
|----------|-------|------------|
| Safety Stock | SS = (Rmax − Rata-rata) × LT | Persediaan pengaman |
| Minimum Stock | Min = (Rata-rata × LT) + SS | Batas stok minimal |
| Maximum Stock | Max = SS + (Rmax × LT) | Batas stok maksimal |
| Order Quantity | OQ = Max − Min | Jumlah pemesaran |

Keterangan:
- **Rmax** = Pemakaian maksimum bahan baku per hari
- **Rata-rata** = Rata-rata pemakaian bahan baku per hari
- **LT** = Lead Time (waktu tunggu pemesanan)

---

## Rumus di Aplikasi

| Komponen | Rumus | Keterangan |
|----------|-------|------------|
| Safety Stock | SS = (Rmax − Rata-rata) × LT | Persediaan pengaman |
| Minimum Stock | Min = Rmax × LT | Batas stok minimal |
| Maximum Stock | Max = SS + Min | Batas stok maksimal |
| Order Quantity | OQ = Max − Min | Jumlah pemesaran |

---

## Kenapa Bisa Berbeda Tapi Hasilnya Sama?

Dokumen TA menjabarkan rumus **Minimum Stock** secara lebih panjang:

```
Min = (Rata-rata × LT) + SS
```

Sedangkan aplikasi menggunakan bentuk yang **lebih sederhana**:

```
Min = Rmax × LT
```

Keduanya menghasilkan angka yang sama karena **SS sudah mengandung komponen (Rmax − Rata-rata) × LT**. Ketika SS dijabarkan, komponen `(Rata-rata × LT)` akan saling menghilangkan.

Hal yang sama berlaku untuk **Maximum Stock**. Dokumen menulis:

```
Max = SS + (Rmax × LT)
```

Aplikasi menulis:

```
Max = SS + Min
```

Karena Min = Rmax × LT, maka Max di aplikasi = SS + (Rmax × LT) yang sama persis dengan dokumen.

---

## Pembuktian Matematis

### 1. Safety Stock

```
SS (dokumen) = (Rmax − Rata-rata) × LT
SS (aplikasi) = (Rmax − Rata-rata) × LT
```

Sama persis ✅

### 2. Minimum Stock

```
Min (dokumen) = (Rata-rata × LT) + SS
             = (Rata-rata × LT) + (Rmax − Rata-rata) × LT
             = (Rata-rata × LT) + (Rmax × LT) − (Rata-rata × LT)
             = Rmax × LT

Min (aplikasi) = Rmax × LT
```

Sama persis ✅

### 3. Maximum Stock

```
Max (dokumen) = SS + (Rmax × LT)

Max (aplikasi) = SS + Min
               = SS + (Rmax × LT)
```

Sama persis ✅

### 4. Order Quantity

```
OQ (dokumen) = Max − Min
OQ (aplikasi) = Max − Min
```

Sama persis ✅

---

## Tabel Pembuktian dengan Data Real (LT = 1)

| Bahan | Rmax | Rata-rata | LT | SS | Min (doc) | Min (app) | Max (doc) | Max (app) | OQ |
|-------|------|-----------|----|----|-----------|-----------|-----------|-----------|----|
| Beras | 22 | 16.25 | 1 | 5.75 | 22 | 22 | 27.75 | 27.75 | 5.75 |
| Ayam | 10 | 7.25 | 1 | 2.75 | 10 | 10 | 12.75 | 12.75 | 2.75 |
| Tempe | 10 | 7 | 1 | 3 | 10 | 10 | 13 | 13 | 3 |
| Tahu | 12 | 9 | 1 | 3 | 12 | 12 | 15 | 15 | 3 |
| Udang | 8 | 6.5 | 1 | 1.5 | 8 | 8 | 9.5 | 9.5 | 1.5 |
| Cabai Merah | 6 | 5.5 | 1 | 0.5 | 6 | 6 | 6.5 | 6.5 | 0.5 |
| Cabai Rawit | 5 | 3.5 | 1 | 1.5 | 5 | 5 | 6.5 | 6.5 | 1.5 |
| Bebek | 9 | 7 | 1 | 2 | 9 | 9 | 11 | 11 | 2 |
| Cumi | 8 | 5.75 | 1 | 2.25 | 8 | 8 | 10.25 | 10.25 | 2.25 |
| Lele | 7 | 6 | 1 | 1 | 7 | 7 | 8 | 8 | 1 |

Semua 10 bahan menghasilkan **nilai yang identik**.

---

## Pembuktian dengan Lead Time Berbeda (LT = 2)

Untuk membuktikan bahwa hasil tetap sama untuk LT berapa pun, berikut contoh menggunakan data Beras dengan LT = 2:

| Variabel | Nilai |
|----------|-------|
| Rmax | 22 |
| Rata-rata | 16.25 |
| LT | 2 |
| SS | (22 − 16.25) × 2 = 11.5 |

| Rumus | Dokumen TA | Aplikasi |
|-------|-----------|----------|
| **Min** | (16.25 × 2) + 11.5 = 32.5 + 11.5 = **44** | 22 × 2 = **44** |
| **Max** | 11.5 + (22 × 2) = 11.5 + 44 = **55.5** | 11.5 + 44 = **55.5** |

Hasil tetap sama.

---

## Kesimpulan

1. **Rumus di aplikasi dan dokumen TA identik secara matematis.**
2. Perbedaan hanya pada **cara penulisan**, bukan pada hasil perhitungan.
3. Aplikasi menggunakan bentuk yang **lebih pendek dan efisien** secara komputasi:
   - `Min = Rmax × LT` (lebih sederhana daripada `(Rata × LT) + SS`)
   - `Max = SS + Min` (menghindari pengulangan kalkulasi `Rmax × LT`)
4. Hasilnya **sama untuk semua bahan dan untuk Lead Time berapa pun**.
5. Kalau di laporan TA ditanya kenapa berbeda, jawabannya: **aplikasi menggunakan bentuk sederhana dari rumus yang sama**, dibuktikan dengan penjabaran aljabar di atas.
