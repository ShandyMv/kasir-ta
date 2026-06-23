{{-- Data passed from DashboardController --}}

@extends('layouts.admin')

@section('title', 'Dashboard Admin')

@section('content')
<div class="row">
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-primary bg-opacity-10 me-3" style="color:#5D87FF;">
          <i class="ti ti-box"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalBahan }}</h3>
          <small class="text-muted">Total Bahan Baku</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-building-store"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalSupplier }}</h3>
          <small class="text-muted">Total Supplier</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px;border-radius:12px;background:rgba(111,66,193,0.1);color:#6F42C1;font-size:1.5rem;">
          <i class="ti ti-users"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalUser }}</h3>
          <small class="text-muted">Total User</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-alert-triangle"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokMenipis }}</h3>
          <small class="text-muted">Stok Menipis</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-8">
    <div class="card w-100">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">Grafik Stok Bahan Baku</h5>
        <div id="stockChart" style="height:300px;width:100%;overflow:hidden;">
          <div id="stockChartLoader" class="d-flex align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
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
        <div style="max-height:200px;overflow-y:auto;">
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
        <div style="max-height:200px;overflow-y:auto;">
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
          <i class="ti ti-activity me-2 text-primary"></i>Aktivitas Terbaru
        </h5>
        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th>Waktu</th>
                <th>User</th>
                <th>Aktivitas</th>
                <th>Detail</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($aktivitas as $item)
              <tr>
                <td><span class="fw-medium">{{ $item['waktu'] }}</span></td>
                <td>
                  <span class="badge bg-light text-dark me-1">
                    <i class="ti ti-user me-1"></i>{{ $item['user'] }}
                  </span>
                </td>
                <td>{{ $item['aksi'] }}</td>
                <td>{{ $item['detail'] }}</td>
                <td>
                  <span class="badge bg-{{ $item['status'] }} bg-opacity-10 text-{{ $item['status'] }} px-3 py-1">
                    {{ $item['label'] }}
                  </span>
                </td>
              </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  $(document).ready(function() {
    setTimeout(function() {
      var loaderEl = document.getElementById('stockChartLoader');
      var timeout = setTimeout(function() {
        if (loaderEl) loaderEl.style.display = 'none';
      }, 5000);

      var options = {
        chart: { type: 'bar', height: 300, toolbar: { show: false } },
        series: [{
          name: 'Stok Saat Ini',
          data: @json($chartStok)
        }, {
          name: 'Stok Minimum',
          data: @json($chartMin)
        }],
        xaxis: {
          categories: @json($chartCategories),
          labels: {
            rotate: -45,
            style: { fontSize: '10px' }
          }
        },
        colors: ['#5D87FF', '#DC3545'],
        plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
        dataLabels: { enabled: false },
        legend: { position: 'top' }
      };
      try {
        var chart = new ApexCharts(document.querySelector("#stockChart"), options);
        chart.render().then(function() {
          clearTimeout(timeout);
          if (loaderEl) loaderEl.style.display = 'none';
          setTimeout(function() {
            window.dispatchEvent(new Event('resize'));
          }, 100);
        }).catch(function() {
          clearTimeout(timeout);
          if (loaderEl) loaderEl.style.display = 'none';
        });
      } catch(e) {
        clearTimeout(timeout);
        if (loaderEl) loaderEl.style.display = 'none';
      }
    });
  });
</script>
@endpush
