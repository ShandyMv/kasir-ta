@php
function getStatusStok($code) {
    $map = [
        'BERLEBIH' => ['label' => 'Berlebih', 'class' => 'dark'],
        'AMAN' => ['label' => 'Aman', 'class' => 'success'],
        'SEGERA_ROP' => ['label' => 'Segera Restock', 'class' => 'warning'],
        'KRITIS' => ['label' => 'Kritis', 'class' => 'danger'],
    ];
    return $map[$code] ?? ['label' => '-', 'class' => 'secondary'];
}

function getRekomendasi($stok, $safety, $min, $max) {
    if ($stok > $max) return ['text' => 'Hentikan order', 'class' => 'text-danger'];
    if ($stok > $min) return ['text' => '-', 'class' => 'text-success'];
    if ($stok > $safety) return ['text' => number_format($min - $stok, 0) . ' (segera)', 'class' => 'text-warning fw-medium'];
    return ['text' => number_format($safety - $stok, 0) . ' (kritis)', 'class' => 'text-danger fw-medium'];
}

function getProgress($stok, $max) {
    if ($max <= 0) return 0;
    return min(100, ($stok / $max) * 100);
}
@endphp

@extends('layouts.admin')

@section('title', 'Min-Max Analysis')

@section('content')
<div class="row mb-4">
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-shield-check" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-success">{{ $statAman }}</h3>
          <small class="text-muted">Bahan Aman</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-alert-circle" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-warning">{{ $statSegera }}</h3>
          <small class="text-muted">Segera Restock</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-alert-triangle" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-danger">{{ $statKritis }}</h3>
          <small class="text-muted">Kritis</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-dark bg-opacity-10 me-3" style="color:#6C757D;">
          <i class="ti ti-archive-up" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark">{{ $statBerlebih }}</h3>
          <small class="text-muted">Stok Berlebih</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-chart-bar me-2" style="color:#D63384;"></i>Analisis Min-Max Stok Bahan Baku
      </h5>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:250px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ $search }}">
      </div>
      <select name="status" class="form-select" style="max-width:180px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="AMAN" {{ $status === 'AMAN' ? 'selected' : '' }}>Aman</option>
        <option value="SEGERA_ROP" {{ $status === 'SEGERA_ROP' ? 'selected' : '' }}>Segera Restock</option>
        <option value="KRITIS" {{ $status === 'KRITIS' ? 'selected' : '' }}>Kritis</option>
        <option value="BERLEBIH" {{ $status === 'BERLEBIH' ? 'selected' : '' }}>Berlebih</option>
      </select>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search || $status)
        <a href="{{ route('min-max-analysis') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Bahan</th>
            <th>Current Stock</th>
            <th class="d-none">Lead Time</th>
            <th class="d-none">Rmax Daily</th>
            <th class="d-none">Safety Stock</th>
            <th>Min Stock</th>
            <th>Max Stock</th>
            <th>Order Qty</th>
            <th>Progress</th>
            <th>Status</th>
            <th class="d-none">Rekomendasi</th>
            <th class="d-none">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bahanBakus as $i => $b)
            @php
              $statusStok = getStatusStok($b->status_code);
              $rekomendasi = getRekomendasi(
                  (float) $b->stok_saat_ini,
                  (float) $b->safety_stock_calc,
                  (float) $b->min_stock_calc,
                  (float) $b->max_stock_calc
              );
              $progress = getProgress((float) $b->stok_saat_ini, (float) $b->max_stock_calc);
            @endphp
          <tr>
            <td>{{ $bahanBakus->firstItem() + $i }}</td>
            <td class="fw-medium">{{ $b->nama_bahan }} <small class="text-muted">({{ $b->kode_bahan }})</small></td>
            <td>
              <span class="fw-bold">{{ number_format($b->stok_saat_ini, 0) }}</span>
              <small class="text-muted">{{ $b->satuan->nama_satuan }}</small>
            </td>
            <td class="d-none">{{ $b->lead_time_val }} hari</td>
            <td class="fw-medium d-none">{{ number_format($b->rmax_daily, 0) }}</td>
            <td class="d-none">{{ number_format($b->safety_stock_calc, 0) }}</td>
            <td>{{ number_format($b->min_stock_calc, 0) }}</td>
            <td>{{ number_format($b->max_stock_calc, 0) }}</td>
            <td>
              @if($b->status_code === 'SEGERA_ROP' || $b->status_code === 'KRITIS')
                @if($b->order_qty > 0)
                  {{ number_format($b->order_qty, 0) }}
                @else
                  -
                @endif
              @else
                -
              @endif
            </td>
            <td style="min-width:120px;">
              <div class="d-flex align-items-center gap-2">
                <div class="progress" style="width:80px;height:8px;">
                  <div class="progress-bar bg-{{ $statusStok['class'] }}" style="width:{{ $progress }}%;" role="progressbar"></div>
                </div>
                <small class="text-muted">{{ number_format($progress, 0) }}%</small>
              </div>
            </td>
            <td>
              <span class="badge bg-{{ $statusStok['class'] }} bg-opacity-10 text-{{ $statusStok['class'] }} px-3 py-1">
                {{ $statusStok['label'] }}
              </span>
            </td>
            <td class="{{ $rekomendasi['class'] }} d-none">
              @if($rekomendasi['text'] === '-')
                <i class="ti ti-check me-1"></i>Aman
              @else
                {{ $rekomendasi['text'] }}
              @endif
            </td>
            <td class="d-none">
              @if($b->status_code === 'SEGERA_ROP' || $b->status_code === 'KRITIS')
                <form action="{{ route('min-max-analysis.apply', $b->id) }}" method="POST" class="d-inline">
                  @csrf
                  <input type="hidden" name="min_stock" value="{{ $b->min_stock_calc }}">
                  <input type="hidden" name="max_stock" value="{{ $b->max_stock_calc }}">
                  <button type="submit" class="btn btn-sm btn-success">
                    <i class="ti ti-check"></i> Terapkan
                  </button>
                </form>
              @else
                <span class="text-muted small">-</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="text-center text-muted py-4">Belum ada data bahan baku.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $bahanBakus->links() }}
    </div>
  </div>
</div>
@endsection