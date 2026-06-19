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
  <div class="col-lg-7 d-flex align-items-stretch">
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
  <div class="col-lg-5">
    <div class="card w-100">
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
