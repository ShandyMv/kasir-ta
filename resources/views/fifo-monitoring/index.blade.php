@php
$kritis = 0;
$waspada = 0;
$normal = 0;
$allBatches = collect();
foreach ($bahanBakus as $b) {
    foreach ($b->fifoBatches as $fb) {
        $usia = (int) $fb->tanggal_masuk->diffInDays(now());
        if ($usia > 30) $kritis++;
        elseif ($usia > 15) $waspada++;
        else $normal++;
        $allBatches->push((object)[
            'bahan' => $b->nama_bahan,
            'batch_kode' => $fb->batch_kode,
            'tanggal_masuk' => $fb->tanggal_masuk,
            'sisa_stok' => $fb->sisa_stok,
            'satuan' => $b->satuan->nama_satuan,
            'usia' => $usia,
        ]);
    }
}
@endphp

@extends('layouts.admin')

@section('title', 'FIFO Monitoring')

@section('content')
<div class="row mb-4">
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-flame" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-danger">{{ $kritis }}</h3>
          <small class="text-muted">Batch Kritis (&gt;30 hari)</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-clock" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-warning">{{ $waspada }}</h3>
          <small class="text-muted">Batch Waspada (15-30 hari)</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-shield-check" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-success">{{ $normal }}</h3>
          <small class="text-muted">Batch Normal (&lt;15 hari)</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-eye me-2 text-info"></i>Monitoring FIFO - Prioritas Penggunaan Bahan
      </h5>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:250px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('fifo-monitoring') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Bahan Baku</th>
            <th>Batch</th>
            <th>Tanggal Masuk</th>
            <th>Sisa Stok Batch</th>
            <th>Usia</th>
            <th>Indikator</th>
            <th>Prioritas</th>
          </tr>
        </thead>
        <tbody>
          @forelse($allBatches as $i => $b)
          @php
            $level = $b->usia > 30 ? 'danger' : ($b->usia > 15 ? 'warning' : 'success');
            $usibar = min(100, ($b->usia / 45) * 100);
            $indicatorClass = match($level) {
              'danger' => 'bg-danger',
              'warning' => 'bg-warning',
              'success' => 'bg-success',
            };
          @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td class="fw-medium">{{ $b->bahan }}</td>
            <td><code>{{ $b->batch_kode }}</code></td>
            <td>{{ $b->tanggal_masuk->format('d M Y') }}</td>
            <td>
              <span class="fw-bold">{{ number_format($b->sisa_stok, 2) }}</span>
              <small class="text-muted">{{ $b->satuan }}</small>
            </td>
            <td>{{ $b->usia }} hari</td>
            <td>
              <div class="d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle" style="width:12px;height:12px;background:{{ $level === 'danger' ? '#DC3545' : ($level === 'warning' ? '#FFAE1F' : '#39B69A') }};"></span>
                <div class="progress" style="width:80px;height:8px;">
                  <div class="progress-bar {{ $indicatorClass }}" style="width:{{ $usibar }}%;" role="progressbar"></div>
                </div>
              </div>
            </td>
            <td>
              @if($level === 'danger')
                <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>Prioritas!</span>
              @elseif($level === 'warning')
                <span class="badge bg-warning text-dark"><i class="ti ti-alert-circle me-1"></i>Waspada</span>
              @else
                <span class="badge bg-success"><i class="ti ti-check me-1"></i>Normal</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada batch dengan stok tersisa.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $bahanBakus->appends(request()->query())->links() }}
    </div>

    <div class="mt-4 p-3 rounded" style="background:#f8f9fa;">
      <h6 class="fw-semibold mb-2">
        <i class="ti ti-info-circle me-1 text-primary"></i>Keterangan Indikator Warna
      </h6>
      <div class="d-flex flex-wrap gap-4">
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#DC3545;"></span> <strong>Merah</strong> = Batch paling lama, priority penggunaan</span>
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#FFAE1F;"></span> <strong>Kuning</strong> = Batch mendekati lama, perlu diperhatikan</span>
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#39B69A;"></span> <strong>Hijau</strong> = Batch baru, masih aman</span>
      </div>
    </div>
  </div>
</div>
@endsection
