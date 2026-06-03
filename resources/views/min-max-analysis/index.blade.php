@php
function getStatusStok($stok, $min, $max) {
    if ($stok > $max) return ['label' => 'Berlebih', 'class' => 'dark'];
    if ($stok < $min) return ['label' => 'Perlu Restock', 'class' => 'danger'];
    return ['label' => 'Aman', 'class' => 'success'];
}

function getRekomendasi($stok, $min, $max) {
    if ($stok > $max) return ['text' => 'Hentikan order', 'class' => 'text-danger'];
    if ($stok < $min) return ['text' => number_format($min - $stok, 0) . ' (segera)', 'class' => 'text-danger fw-medium'];
    if ($stok >= $min && $stok <= $max) return ['text' => '-', 'class' => 'text-success'];
    return ['text' => '-', 'class' => 'text-success'];
}

function getProgress($stok, $max) {
    if ($max <= 0) return 0;
    return min(100, ($stok / $max) * 100);
}

$statAman = 0;
$statRestock = 0;
$statBerlebih = 0;

foreach ($bahanBakus as $b) {
    if ($b->stok_saat_ini > $b->stok_maksimum) $statBerlebih++;
    elseif ($b->stok_saat_ini < $b->stok_minimum) $statRestock++;
    else $statAman++;
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
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-alert-triangle" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-danger">{{ $statRestock }}</h3>
          <small class="text-muted">Perlu Restock</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-secondary bg-opacity-10 me-3" style="color:#6C757D;">
          <i class="ti ti-archive-up" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-dark">{{ $statBerlebih }}</h3>
          <small class="text-muted">Stok Berlebih</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-primary bg-opacity-10 me-3" style="color:#5D87FF;">
          <i class="ti ti-box" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $bahanBakus->total() }}</h3>
          <small class="text-muted">Total Bahan</small>
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
      <select name="status" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="aman" {{ $status === 'aman' ? 'selected' : '' }}>Aman</option>
        <option value="restock" {{ $status === 'restock' ? 'selected' : '' }}>Perlu Restock</option>
        <option value="berlebih" {{ $status === 'berlebih' ? 'selected' : '' }}>Berlebih</option>
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
            <th>Minimum Stock</th>
            <th>Maximum Stock</th>
            <th>Progress</th>
            <th>Status</th>
            <th>Rekomendasi Order</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bahanBakus as $i => $b)
            @php
              $statusStok = getStatusStok($b->stok_saat_ini, $b->stok_minimum, $b->stok_maksimum);
              $rekomendasi = getRekomendasi($b->stok_saat_ini, $b->stok_minimum, $b->stok_maksimum);
              $progress = getProgress($b->stok_saat_ini, $b->stok_maksimum);
            @endphp
          <tr>
            <td>{{ $bahanBakus->firstItem() + $i }}</td>
            <td class="fw-medium">{{ $b->nama_bahan }} <small class="text-muted">({{ $b->kode_bahan }})</small></td>
            <td>
              <span class="fw-bold">{{ number_format($b->stok_saat_ini, 0) }}</span>
              <small class="text-muted">{{ $b->satuan->nama_satuan }}</small>
            </td>
            <td>{{ number_format($b->stok_minimum, 0) }}</td>
            <td>{{ number_format($b->stok_maksimum, 0) }}</td>
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
            <td class="{{ $rekomendasi['class'] }}">
              @if($rekomendasi['text'] === '-')
                <i class="ti ti-check me-1"></i>Aman
              @else
                {{ $rekomendasi['text'] }}
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada data bahan baku.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $bahanBakus->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
