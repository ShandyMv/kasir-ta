{{-- Data passed from DashboardController --}}

@extends('layouts.admin')

@section('title', 'Dashboard Karyawan')

@section('content')
<div class="row">
  <div class="col-lg-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-download" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokMasukHariIni }}</h3>
          <small class="text-muted">Stok Masuk Hari Ini</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-upload" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokKeluarHariIni }}</h3>
          <small class="text-muted">Stok Keluar Hari Ini</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-8">
    <div class="card w-100">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-list-check me-2 text-danger"></i>FIFO Priority List
        </h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Bahan</th>
                <th>Batch</th>
                <th>Tanggal Masuk</th>
                <th>Sisa Stok</th>
                <th>Usia</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($fifoPriority as $item)
              <tr>
                <td class="fw-medium">{{ $item['bahan'] }}</td>
                <td><code>{{ $item['batch'] }}</code></td>
                <td>{{ $item['tanggal'] }}</td>
                <td>{{ $item['sisa'] }}</td>
                <td>{{ $item['usia'] }}</td>
                <td>
                  <span class="badge bg-{{ $item['level'] }}">{{ $item['label'] }}</span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4 d-flex flex-column gap-3">
    {{-- Card Expired --}}
    <div class="card w-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2">
          <i class="ti ti-clock text-danger me-2 fs-5"></i>
          <h6 class="fw-semibold mb-0">Expired</h6>
          @if(count($expiredNotif) > 0)
            <span class="badge bg-danger ms-auto">{{ count($expiredNotif) }}</span>
          @endif
        </div>
        <div style="max-height:180px;overflow-y:auto;">
          @forelse($expiredNotif as $n)
            <div class="d-flex align-items-start py-2 px-2 mb-1 rounded" style="font-size:.8rem;background:#fef2f2;border-left:3px solid #dc3545;">
              <i class="ti ti-clock text-danger me-2 mt-1 fs-6"></i>
              <div>
                <span class="fw-medium">{{ $n['bahan'] }}</span><br>
                <small class="text-muted">{{ $n['batch'] }} — expired {{ $n['expired_at'] }} (sisa {{ $n['sisa'] }} {{ $n['satuan'] }})</small>
              </div>
            </div>
          @empty
            <div class="text-center text-muted py-3">
              <i class="ti ti-check-circle fs-4 d-block mb-1"></i>
              <small>Tidak ada expired</small>
            </div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Card Restock --}}
    <div class="card w-100">
      <div class="card-body">
        <div class="d-flex align-items-center mb-2">
          <i class="ti ti-alert-triangle text-warning me-2 fs-5"></i>
          <h6 class="fw-semibold mb-0">Perlu Restock</h6>
          @if(count($restockNotif) > 0)
            <span class="badge bg-warning text-dark ms-auto">{{ count($restockNotif) }}</span>
          @endif
        </div>
        <div style="max-height:180px;overflow-y:auto;">
          @forelse($restockNotif as $n)
            <div class="d-flex align-items-start py-2 px-2 mb-1 rounded" style="font-size:.8rem;background:#fffbe6;border-left:3px solid #ffc107;">
              <i class="ti ti-alert-triangle text-warning me-2 mt-1 fs-6"></i>
              <div>
                <span class="fw-medium">{{ $n['bahan'] }}</span><br>
                <small class="text-muted">stok {{ $n['stok'] }} {{ $n['satuan'] }} < {{ $n['min'] }} {{ $n['satuan'] }}</small>
              </div>
            </div>
          @empty
            <div class="text-center text-muted py-3">
              <i class="ti ti-check-circle fs-4 d-block mb-1"></i>
              <small>Stok semua aman</small>
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-activity me-2 text-primary"></i>Aktivitas Hari Ini
        </h5>
        <ul class="timeline-widget mb-0 position-relative">
          @foreach($aktivitas as $item)
          <li class="timeline-item d-flex position-relative overflow-hidden mb-3">
            <div class="timeline-time text-dark flex-shrink-0 text-end" style="min-width:55px;font-size:.8rem;">
              {{ $item['waktu'] }}
            </div>
            <div class="timeline-badge-wrap d-flex flex-column align-items-center mx-2">
              <span class="timeline-badge border-2 border border-{{ $item['status'] }} flex-shrink-0 my-2"
                    style="width:10px;height:10px;border-radius:50%;display:inline-block;"></span>
              <span class="timeline-badge-border d-block flex-shrink-0" style="width:2px;height:100%;background:#e9ecef;"></span>
            </div>
            <div class="timeline-desc fs-3 text-dark mt-n1">
              <span class="fw-medium">{{ $item['aksi'] }}</span>
              <p class="mb-0 text-muted" style="font-size:.8rem;">{{ $item['detail'] }}</p>
            </div>
          </li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection
