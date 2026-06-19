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
  <div class="col-lg-7 d-flex align-items-stretch">
    <div class="card w-100">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-3">
          <i class="ti ti-chart-area me-2 text-primary"></i>Grafik Persediaan
        </h5>
        <div id="ownerChart" style="height:280px;width:100%;overflow:hidden;">
          <div id="ownerChartLoader" class="d-flex align-items-center justify-content-center h-100">
            <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-5">
    <div class="card w-100">
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
        <hr>
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
        chart: { type: 'area', height: 280, toolbar: { show: false } },
        series: [{
          name: 'Total Persediaan',
          data: @json($chartData)
        }],
        xaxis: {
          categories: @json($chartLabels),
        },
        colors: ['#5D87FF'],
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.5,
            opacityTo: 0.1
          }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 2 },
        markers: { size: 4 }
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
