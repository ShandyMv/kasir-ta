{{-- Data passed from DashboardController --}}

@extends('layouts.admin')

@section('title', 'Dashboard Owner')

@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-shield-check" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $amanCount }}</h3>
          <small class="text-muted">Stok Aman</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-alert-triangle" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokMenipis }}</h3>
          <small class="text-muted">Stok Menipis</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-shopping-cart" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $rekomendasiRestock }}</h3>
          <small class="text-muted">Rekomendasi Restock</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row mt-3">
  <div class="col-lg-7">
    <div class="card w-100">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-chart-bar me-2 text-primary"></i>Grafik Stok Bahan Baku
        </h5>
        <div id="ownerChart" style="height:280px;width:100%;overflow:hidden;">
          <div id="ownerChartLoader" class="d-flex align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-5 d-flex flex-column gap-3">
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
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-eye me-2 text-info"></i>Ringkasan FIFO
        </h5>
        <div class="mb-3">
          <div class="d-flex justify-content-between mb-2">
            <small class="text-muted">Batch Tertua</small>
            <span class="fw-medium text-danger">{{ $ringkasanFifo['batchTertua'] }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <small class="text-muted">Batch Terbaru</small>
            <span class="fw-medium text-success">{{ $ringkasanFifo['batchTerbaru'] }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <small class="text-muted">Total Batch Aktif</small>
            <span class="fw-medium">{{ $ringkasanFifo['totalBatch'] }}</span>
          </div>
          <div class="d-flex justify-content-between">
            <small class="text-muted">Batch Kritis (>30 hari)</small>
            <span class="fw-medium text-danger">{{ $ringkasanFifo['batchKritis'] }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-chart-bar me-2 text-warning"></i>Ringkasan Min-Max
        </h5>
        <div class="row text-center">
          <div class="col-3">
            <div class="p-2 rounded bg-success bg-opacity-10">
              <h5 class="mb-0 text-success fw-bold">{{ $ringkasanMinMax['aman'] }}</h5>
              <small class="text-muted">Aman</small>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded bg-warning bg-opacity-10">
              <h5 class="mb-0 text-warning fw-bold">{{ $ringkasanMinMax['waspada'] }}</h5>
              <small class="text-muted">Waspada</small>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded bg-danger bg-opacity-10">
              <h5 class="mb-0 text-danger fw-bold">{{ $ringkasanMinMax['kritis'] }}</h5>
              <small class="text-muted">Kritis</small>
            </div>
          </div>
          <div class="col-3">
            <div class="p-2 rounded bg-dark bg-opacity-10">
              <h5 class="mb-0 text-dark fw-bold">{{ $ringkasanMinMax['berlebih'] }}</h5>
              <small class="text-muted">Berlebih</small>
            </div>
          </div>
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
      var loaderEl = document.getElementById('ownerChartLoader');
      var timeout = setTimeout(function() {
        if (loaderEl) loaderEl.style.display = 'none';
      }, 5000);

      var options = {
        chart: { type: 'bar', height: 280, toolbar: { show: false } },
        series: [{
          name: 'Stok Saat Ini',
          data: @json($chartData)
        }, {
          name: 'Stok Minimum',
          data: @json($chartMin)
        }],
        xaxis: {
          categories: @json($chartLabels),
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
        var chart = new ApexCharts(document.querySelector("#ownerChart"), options);
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
