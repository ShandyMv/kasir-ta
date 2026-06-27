<?php

namespace App\Exports;

use App\Models\StokMasuk;
use App\Models\StokKeluar;
use App\Models\BahanBaku;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    protected string $type;
    protected ?string $startDate;
    protected ?string $endDate;
    protected ?string $bahanBakuId;

    public function __construct(string $type, ?string $startDate, ?string $endDate, ?string $bahanBakuId)
    {
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->bahanBakuId = $bahanBakuId;
    }

    public function collection()
    {
        if ($this->type === 'stok-masuk') {
            $query = StokMasuk::with(['bahanBaku.satuan', 'supplier', 'user']);
            if ($this->startDate) $query->whereDate('tanggal_masuk', '>=', $this->startDate);
            if ($this->endDate) $query->whereDate('tanggal_masuk', '<=', $this->endDate);
            if ($this->bahanBakuId) $query->where('bahan_baku_id', $this->bahanBakuId);
            return $query->latest()->get();
        }

        if ($this->type === 'stok-keluar') {
            $query = StokKeluar::with(['bahanBaku.satuan', 'user']);
            if ($this->startDate) $query->whereDate('tanggal_keluar', '>=', $this->startDate);
            if ($this->endDate) $query->whereDate('tanggal_keluar', '<=', $this->endDate);
            if ($this->bahanBakuId) $query->where('bahan_baku_id', $this->bahanBakuId);
            return $query->latest()->get();
        }

        $query = BahanBaku::with('satuan');
        if ($this->bahanBakuId) $query->where('id', $this->bahanBakuId);
        return $query->get();
    }

    public function headings(): array
    {
        return match ($this->type) {
            'stok-masuk' => [
                'No', 'Tanggal', 'Bahan Baku', 'Supplier', 'Jumlah', 'Satuan', 'Batch', 'Input Oleh'
            ],
            'stok-keluar' => [
                'No', 'Tanggal', 'Bahan Baku', 'Jumlah Keluar', 'Satuan', 'Keterangan', 'Input Oleh'
            ],
            default => [
                'No', 'Kode', 'Nama Bahan', 'Satuan', 'Stok Saat Ini', 'Stok Minimum', 'Stok Maksimum', 'Status'
            ],
        };
    }

    public function map($row): array
    {
        static $i = 0;
        $i++;

        if ($this->type === 'stok-masuk') {
            return [
                $i,
                $row->tanggal_masuk->format('d/m/Y'),
                $row->bahanBaku->nama_bahan,
                $row->supplier->nama_supplier,
                number_format($row->jumlah, 2),
                $row->bahanBaku->satuan->nama_satuan,
                $row->batch_kode,
                $row->user->name,
            ];
        }

        if ($this->type === 'stok-keluar') {
            return [
                $i,
                $row->tanggal_keluar->format('d/m/Y'),
                $row->bahanBaku->nama_bahan,
                number_format($row->jumlah_keluar, 2),
                $row->bahanBaku->satuan->nama_satuan,
                $row->keterangan ?? '-',
                $row->user->name,
            ];
        }

        $status = $row->stok_saat_ini < $row->stok_minimum ? 'Restock' : 'Aman';
        return [
            $i,
            $row->kode_bahan,
            $row->nama_bahan,
            $row->satuan->nama_satuan,
            number_format($row->stok_saat_ini, 0),
            number_format($row->stok_minimum, 0),
            number_format($row->stok_maksimum, 0),
            $status,
        ];
    }

    public function title(): string
    {
        return match ($this->type) {
            'stok-masuk' => 'Stok Masuk',
            'stok-keluar' => 'Stok Keluar',
            default => 'Persediaan',
        };
    }
}
