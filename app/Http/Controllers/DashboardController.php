<?php

namespace App\Http\Controllers;

use App\Models\BahanBaku;
use App\Models\Supplier;
use App\Models\User;
use App\Models\StokMasuk;
use App\Models\StokKeluar;
use App\Models\StokKeluarDetail;
use App\Models\FifoBatch;
use App\Services\MinMaxAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    protected $minMax;

    public function __construct(MinMaxAnalysisService $minMax)
    {
        $this->minMax = $minMax;
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return $this->admin();
        }
        if ($user->isKaryawan()) {
            return $this->karyawan();
        }
        if ($user->isOwner()) {
            return $this->owner();
        }

        return view('dashboard');
    }

    protected function admin()
    {
        $totalBahan = BahanBaku::count();
        $totalSupplier = Supplier::count();
        $totalUser = User::count();
        $stokMenipis = $this->minMax->countRestock();

        $notifikasi = $this->getNotifications();

        $aktivitas = StokKeluar::with('user', 'bahanBaku', 'bahanBaku.satuan')
            ->latest()->take(5)->get()->map(function ($sk) {
                return [
                    'waktu' => $sk->created_at->format('H:i'),
                    'user' => $sk->user->username,
                    'aksi' => 'Stok Keluar',
                    'detail' => $sk->bahanBaku->nama_bahan . ' ' . number_format($sk->jumlah_keluar, 2) . ' ' . $sk->bahanBaku->satuan->nama_satuan,
                    'status' => 'success',
                    'label' => 'Selesai',
                ];
            });

        $stokMasukAktivitas = StokMasuk::with('user', 'bahanBaku', 'bahanBaku.satuan')
            ->latest()->take(3)->get()->map(function ($sm) {
                return [
                    'waktu' => $sm->created_at->format('H:i'),
                    'user' => $sm->user->username,
                    'aksi' => 'Stok Masuk',
                    'detail' => $sm->bahanBaku->nama_bahan . ' ' . number_format($sm->jumlah, 2) . ' ' . $sm->bahanBaku->satuan->nama_satuan,
                    'status' => 'success',
                    'label' => 'Selesai',
                ];
            });

        $aktivitas = $stokMasukAktivitas->merge($aktivitas)->sortByDesc('waktu')->take(5);

        $bahanBakus = BahanBaku::with('satuan')->orderBy('stok_saat_ini', 'desc')->take(10)->get();
        $chartCategories = $bahanBakus->pluck('nama_bahan')->toArray();
        $chartStok = $bahanBakus->pluck('stok_saat_ini')->map(fn($v) => (float) $v)->toArray();
        $chartMin = $bahanBakus->pluck('stok_minimum')->map(fn($v) => (float) $v)->toArray();

        return view('dashboard.admin', compact(
            'totalBahan', 'totalSupplier', 'totalUser', 'stokMenipis',
            'notifikasi', 'aktivitas', 'chartCategories', 'chartStok', 'chartMin'
        ));
    }

    protected function karyawan()
    {
        $stokMasukHariIni = StokMasuk::whereDate('created_at', today())->count();
        $stokKeluarHariIni = StokKeluar::whereDate('created_at', today())->count();

        $fifoPriority = FifoBatch::with('bahanBaku', 'bahanBaku.satuan')
            ->where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk')
            ->take(4)
            ->get()
            ->map(function ($fb) {
                $usia = (int) $fb->tanggal_masuk->diffInDays(now());
                $level = $usia > 30 ? 'danger' : ($usia > 15 ? 'warning' : 'success');
                $label = $usia > 30 ? 'PRIORITAS!' : ($usia > 15 ? 'Segera' : ($usia > 5 ? 'Normal' : 'Baru'));
                return [
                    'bahan' => $fb->bahanBaku->nama_bahan,
                    'batch' => $fb->batch_kode,
                    'tanggal' => $fb->tanggal_masuk->format('d M Y'),
                    'sisa' => number_format($fb->sisa_stok, 2) . ' ' . $fb->bahanBaku->satuan->nama_satuan,
                    'usia' => $usia . ' hari',
                    'level' => $level,
                    'label' => $label,
                ];
            });

        $aktivitas = StokKeluar::with('user', 'bahanBaku', 'bahanBaku.satuan')
            ->whereDate('created_at', today())
            ->latest()->take(5)->get()->map(function ($sk) {
                return [
                    'waktu' => $sk->created_at->format('H:i'),
                    'aksi' => 'Stok Keluar',
                    'detail' => $sk->bahanBaku->nama_bahan . ' ' . number_format($sk->jumlah_keluar, 2) . ' ' . $sk->bahanBaku->satuan->nama_satuan . ' oleh ' . $sk->user->username,
                    'status' => 'success',
                ];
            });

        $masukAktivitas = StokMasuk::with('user', 'bahanBaku', 'bahanBaku.satuan')
            ->whereDate('created_at', today())
            ->latest()->take(5)->get()->map(function ($sm) {
                return [
                    'waktu' => $sm->created_at->format('H:i'),
                    'aksi' => 'Stok Masuk',
                    'detail' => $sm->bahanBaku->nama_bahan . ' ' . number_format($sm->jumlah, 2) . ' ' . $sm->bahanBaku->satuan->nama_satuan . ' dari ' . ($sm->supplier->nama_supplier ?? '-'),
                    'status' => 'success',
                ];
            });

        $aktivitas = $masukAktivitas->merge($aktivitas)->sortByDesc('waktu')->take(5);

        return view('dashboard.karyawan', compact(
            'stokMasukHariIni', 'stokKeluarHariIni', 'fifoPriority', 'aktivitas'
        ));
    }

    protected function owner()
    {
        $amanCount = $this->minMax->countAman();
        $restockCount = $this->minMax->countRestock();
        $berlebihCount = $this->minMax->countBerlebih();
        $stokMenipis = $restockCount;
        $rekomendasiRestock = $restockCount;

        $oldestBatch = FifoBatch::where('sisa_stok', '>', 0)
            ->orderBy('tanggal_masuk')->with('bahanBaku')->first();
        $newestBatch = FifoBatch::where('sisa_stok', '>', 0)
            ->orderByDesc('tanggal_masuk')->with('bahanBaku')->first();
        $totalBatch = FifoBatch::where('sisa_stok', '>', 0)->count();
        $batchKritis = FifoBatch::where('sisa_stok', '>', 0)
            ->get()->filter(function ($fb) {
                return (int) $fb->tanggal_masuk->diffInDays(now()) > 30;
            })->count();

        $wasCount = FifoBatch::where('sisa_stok', '>', 0)
            ->get()->filter(function ($fb) {
                $usia = (int) $fb->tanggal_masuk->diffInDays(now());
                return $usia > 15 && $usia <= 30;
            })->count();

        $ringkasanFifo = [
            'batchTertua' => $oldestBatch
                ? $oldestBatch->bahanBaku->nama_bahan . ' - ' . $oldestBatch->batch_kode . ' (' . $oldestBatch->tanggal_masuk->format('d M Y') . ')'
                : '-',
            'batchTerbaru' => $newestBatch
                ? $newestBatch->bahanBaku->nama_bahan . ' - ' . $newestBatch->batch_kode . ' (' . $newestBatch->tanggal_masuk->format('d M Y') . ')'
                : '-',
            'totalBatch' => $totalBatch,
            'batchKritis' => $batchKritis,
        ];

        $segeraCount = $this->minMax->countSegeraROP();
        $kritisCount = $this->minMax->countKritis();

        $ringkasanMinMax = [
            'aman' => $amanCount,
            'waspada' => $segeraCount,
            'kritis' => $kritisCount,
            'berlebih' => $berlebihCount,
        ];

        $dataPersediaan = StokMasuk::select(
            DB::raw("DATE_FORMAT(tanggal_masuk, '%b') as bulan"),
            DB::raw("SUM(jumlah) as stok")
        )->whereYear('tanggal_masuk', now()->year)
            ->groupBy('bulan')
            ->orderByRaw("MIN(tanggal_masuk)")
            ->get();

        if ($dataPersediaan->isEmpty()) {
            $dataPersediaan = collect([
                ['bulan' => 'Jan', 'stok' => 0],
                ['bulan' => 'Feb', 'stok' => 0],
                ['bulan' => 'Mar', 'stok' => 0],
                ['bulan' => 'Apr', 'stok' => 0],
                ['bulan' => 'Mei', 'stok' => 0],
                ['bulan' => 'Jun', 'stok' => 0],
            ]);
        }

        $chartLabels = $dataPersediaan->pluck('bulan')->toArray();
        $chartData = $dataPersediaan->pluck('stok')->map(fn($v) => (float) $v)->toArray();

        return view('dashboard.owner', compact(
            'amanCount', 'stokMenipis', 'rekomendasiRestock',
            'ringkasanFifo', 'ringkasanMinMax', 'chartLabels', 'chartData'
        ));
    }

    protected function getNotifications()
    {
        $notif = collect();

        $criticalBatches = FifoBatch::with('bahanBaku')
            ->where('sisa_stok', '>', 0)
            ->get()
            ->filter(function ($fb) {
                return (int) $fb->tanggal_masuk->diffInDays(now()) > 30;
            })
            ->take(2);

        foreach ($criticalBatches as $fb) {
            $usia = (int) $fb->tanggal_masuk->diffInDays(now());
            $notif->push([
                'type' => 'fifo',
                'icon' => 'ti ti-clock',
                'color' => 'danger',
                'message' => "{$fb->bahanBaku->nama_bahan} {$fb->batch_kode} ({$fb->tanggal_masuk->format('d M')}) sudah {$usia} hari - segera gunakan!",
            ]);
        }

        $restockItems = BahanBaku::whereRaw('stok_saat_ini < stok_minimum')->take(2);
        foreach ($restockItems->get() as $b) {
            $notif->push([
                'type' => 'minmax',
                'icon' => 'ti ti-alert-triangle',
                'color' => 'warning',
                'message' => "{$b->nama_bahan} ({$b->stok_saat_ini}) di bawah stok minimum ({$b->stok_minimum}) - perlu restock!",
            ]);
        }

        return $notif;
    }
}
