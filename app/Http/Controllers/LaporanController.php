<?php

namespace App\Http\Controllers;

use App\Exports\LaporanExport;
use App\Models\StokMasuk;
use App\Models\StokKeluar;
use App\Models\BahanBaku;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'stok-masuk');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $bahanBakuId = $request->get('bahan_baku_id');

        $data = collect();
        $total = 0;

        if ($type === 'stok-masuk') {
            $query = StokMasuk::with(['bahanBaku.satuan', 'supplier', 'user']);
            if ($startDate) $query->whereDate('tanggal_masuk', '>=', $startDate);
            if ($endDate) $query->whereDate('tanggal_masuk', '<=', $endDate);
            if ($bahanBakuId) $query->where('bahan_baku_id', $bahanBakuId);
            $data = $query->latest()->paginate(20);
            $total = $data->sum('jumlah');
        } elseif ($type === 'stok-keluar') {
            $query = StokKeluar::with(['bahanBaku.satuan', 'user']);
            if ($startDate) $query->whereDate('tanggal_keluar', '>=', $startDate);
            if ($endDate) $query->whereDate('tanggal_keluar', '<=', $endDate);
            if ($bahanBakuId) $query->where('bahan_baku_id', $bahanBakuId);
            $data = $query->latest()->paginate(20);
            $total = $data->sum('jumlah_keluar');
        } elseif ($type === 'persediaan') {
            $query = BahanBaku::with('satuan');
            if ($bahanBakuId) $query->where('id', $bahanBakuId);
            $data = $query->paginate(20);
        }

        $bahanBakus = BahanBaku::orderBy('nama_bahan')->get();

        return view('laporan.index', compact(
            'data', 'type', 'startDate', 'endDate', 'bahanBakuId', 'total', 'bahanBakus'
        ));
    }

    public function exportPdf(Request $request)
    {
        $type = $request->get('type', 'stok-masuk');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $bahanBakuId = $request->get('bahan_baku_id');

        $title = match ($type) {
            'stok-masuk' => 'Stok Masuk',
            'stok-keluar' => 'Stok Keluar',
            default => 'Persediaan',
        };

        $subtitle = 'Periode: ' . ($startDate ?: 'Awal') . ' - ' . ($endDate ?: 'Akhir');

        $bahanNama = null;
        if ($bahanBakuId) {
            $bahan = BahanBaku::find($bahanBakuId);
            $bahanNama = $bahan?->nama_bahan;
        }

        $headings = match ($type) {
            'stok-masuk' => ['No', 'Tanggal', 'Bahan Baku', 'Supplier', 'Jumlah', 'Satuan', 'Batch', 'Input Oleh'],
            'stok-keluar' => ['No', 'Tanggal', 'Bahan Baku', 'Jumlah Keluar', 'Satuan', 'Keterangan', 'Input Oleh'],
            default => ['No', 'Kode', 'Nama Bahan', 'Satuan', 'Stok Saat Ini', 'Min', 'Max', 'Status'],
        };

        $rows = $this->getExportData($type, $startDate, $endDate, $bahanBakuId);

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'title', 'subtitle', 'startDate', 'endDate', 'bahanNama',
            'headings', 'rows'
        ));

        return $pdf->download("laporan-{$type}-" . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $type = $request->get('type', 'stok-masuk');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $bahanBakuId = $request->get('bahan_baku_id');

        $filename = "laporan-{$type}-" . now()->format('Ymd') . '.xlsx';

        return Excel::download(
            new LaporanExport($type, $startDate, $endDate, $bahanBakuId),
            $filename
        );
    }

    private function getExportData(string $type, ?string $startDate, ?string $endDate, ?string $bahanBakuId): array
    {
        $rows = [];

        if ($type === 'stok-masuk') {
            $query = StokMasuk::with(['bahanBaku.satuan', 'supplier', 'user']);
            if ($startDate) $query->whereDate('tanggal_masuk', '>=', $startDate);
            if ($endDate) $query->whereDate('tanggal_masuk', '<=', $endDate);
            if ($bahanBakuId) $query->where('bahan_baku_id', $bahanBakuId);
            $items = $query->latest()->get();

            foreach ($items as $i => $d) {
                $rows[] = [
                    $i + 1,
                    $d->tanggal_masuk->format('d/m/Y'),
                    e($d->bahanBaku->nama_bahan),
                    e($d->supplier->nama_supplier),
                    number_format($d->jumlah, 2),
                    e($d->bahanBaku->satuan->nama_satuan),
                    e($d->batch_kode),
                    e($d->user->name),
                ];
            }
        } elseif ($type === 'stok-keluar') {
            $query = StokKeluar::with(['bahanBaku.satuan', 'user']);
            if ($startDate) $query->whereDate('tanggal_keluar', '>=', $startDate);
            if ($endDate) $query->whereDate('tanggal_keluar', '<=', $endDate);
            if ($bahanBakuId) $query->where('bahan_baku_id', $bahanBakuId);
            $items = $query->latest()->get();

            foreach ($items as $i => $d) {
                $rows[] = [
                    $i + 1,
                    $d->tanggal_keluar->format('d/m/Y'),
                    e($d->bahanBaku->nama_bahan),
                    number_format($d->jumlah_keluar, 2),
                    e($d->bahanBaku->satuan->nama_satuan),
                    e($d->keterangan ?? '-'),
                    e($d->user->name),
                ];
            }
        } else {
            $query = BahanBaku::with('satuan');
            if ($bahanBakuId) $query->where('id', $bahanBakuId);
            $items = $query->get();

            foreach ($items as $i => $d) {
                $status = $d->stok_saat_ini < $d->stok_minimum ? 'Restock' : 'Aman';
                $class = $d->stok_saat_ini < $d->stok_minimum ? 'danger' : 'success';
                $rows[] = [
                    $i + 1,
                    e($d->kode_bahan),
                    e($d->nama_bahan),
                    e($d->satuan->nama_satuan),
                    number_format($d->stok_saat_ini, 0),
                    number_format($d->stok_minimum, 0),
                    number_format($d->stok_maksimum, 0),
                    "<span class=\"badge-status bg-{$class}\">{$status}</span>",
                ];
            }
        }

        return $rows;
    }
}
