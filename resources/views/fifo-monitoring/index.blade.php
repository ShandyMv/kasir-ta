@php
$totalBatches = $batches->total();
@endphp

@extends('layouts.admin')

@section('title', 'FIFO Monitoring')

@section('content')
<div class="row mb-4">
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-shield-check" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-success">{{ $normal }}</h3>
          <small class="text-muted">Normal</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-clock" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold text-warning">{{ $waspada }}</h3>
          <small class="text-muted">Waspada</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-opacity-10 me-3" style="color:#FD7E14;">
          <i class="ti ti-flame" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold" style="color:#FD7E14;">{{ $kritis }}</h3>
          <small class="text-muted">Kritis</small>
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
          <h3 class="mb-0 fw-bold text-danger">{{ $expired }}</h3>
          <small class="text-muted">Expired</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-eye me-2 text-info"></i>Monitoring FIFO
      </h5>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:250px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ $search }}">
      </div>
      <select name="filter" class="form-select" style="max-width:160px;" onchange="this.form.submit()">
        <option value="">Semua</option>
        <option value="normal" {{ $filter === 'normal' ? 'selected' : '' }}>Normal</option>
        <option value="waspada" {{ $filter === 'waspada' ? 'selected' : '' }}>Waspada</option>
        <option value="kritis" {{ $filter === 'kritis' ? 'selected' : '' }}>Kritis</option>
        <option value="expired" {{ $filter === 'expired' ? 'selected' : '' }}>Expired</option>
      </select>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search || $filter)
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
            <th>Tgl Masuk</th>
            <th>Tgl Kadaluarsa</th>
            <th class="d-none">Prioritas FIFO</th>
            <th>Sisa Stok</th>
            <th>Sisa Umur</th>
            <th>Indikator</th>
          </tr>
        </thead>
        <tbody>
          @forelse($batches as $batch)
            @php
              $indicatorColor = match($batch->indicator) {
                'expired' => '#DC3545',
                'kritis' => '#FD7E14',
                'waspada' => '#FFAE1F',
                default => '#39B69A',
              };
              $indicatorBg = match($batch->indicator) {
                'expired' => 'bg-danger text-white',
                'kritis' => 'bg-kritis text-white',
                'waspada' => 'bg-warning text-dark',
                default => 'bg-success text-white',
              };
              $indicatorLabel = match($batch->indicator) {
                'expired' => 'Expired',
                'kritis' => 'Kritis',
                'waspada' => 'Waspada',
                default => 'Normal',
              };
              $indicatorIcon = match($batch->indicator) {
                'expired' => 'ti ti-alert-triangle',
                'kritis' => 'ti ti-flame',
                'waspada' => 'ti ti-clock',
                default => 'ti ti-check',
              };
              $sisaUmurLabel = $batch->sisa_umur > 0 ? $batch->sisa_umur . ' hr' : 'sudah ' . abs($batch->sisa_umur) . ' hr';
            @endphp
          <tr style="cursor:pointer;" data-bs-toggle="modal" data-bs-target="#detailBahanModal{{ $batch->bahanBaku->id }}">
            <td>{{ $batches->firstItem() + $loop->index }}</td>
            <td class="fw-medium">{{ $batch->bahanBaku->nama_bahan }}</td>
            <td><code>{{ $batch->batch_kode }}</code></td>
            <td>{{ $batch->tanggal_masuk->format('d M Y') }}</td>
            <td>{{ $batch->tgl_kadaluwarsa->format('d M Y') }}</td>
            <td class="d-none"><span class="badge bg-secondary">#{{ $batch->prioritas }}</span></td>
            <td>
              <span class="fw-bold">{{ number_format($batch->sisa_stok, 2) }}</span>
              <small class="text-muted">{{ $batch->bahanBaku->satuan->nama_satuan }}</small>
            </td>
            <td>{{ $sisaUmurLabel }}</td>
            <td>
              <span class="badge {{ $indicatorBg }}">
                <i class="{{ $indicatorIcon }} me-1" style="font-size:.75rem;"></i>{{ $indicatorLabel }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-4">Tidak ada batch dengan stok tersisa.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $batches->appends(request()->query())->links() }}
    </div>

    <div class="mt-4 p-3 rounded" style="background:#f8f9fa;">
      <h6 class="fw-semibold mb-2">
        <i class="ti ti-info-circle me-1 text-primary"></i>Keterangan Indikator
      </h6>
      <div class="d-flex flex-wrap gap-4">
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#39B69A;"></span> <strong>Normal</strong> = Sisa umur &gt; 50% umur simpan</span>
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#FFAE1F;"></span> <strong>Waspada</strong> = Sisa umur 25-50% umur simpan</span>
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#FD7E14;"></span> <strong>Kritis</strong> = Sisa umur &lt; 25% umur simpan</span>
        <span><span class="d-inline-block rounded-circle me-1" style="width:10px;height:10px;background:#DC3545;"></span> <strong>Expired</strong> = Sudah lewat tanggal kadaluarsa</span>
      </div>
    </div>
  </div>
</div>

@foreach($batchGroups as $bahanId => $group)
<div class="modal fade" id="detailBahanModal{{ $bahanId }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-package me-2 text-info"></i>Detail Batch: {{ $group['nama'] }}
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted">Umur Simpan</span>
            <span class="fw-bold">{{ $group['expiry'] }} hari</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-muted">Total Stok</span>
            <span class="fw-bold">{{ number_format($group['total_stok'], 0) }} {{ $group['satuan'] }}</span>
          </div>
          <div class="d-flex justify-content-between align-items-center mt-1">
            <span class="text-muted">Jumlah Batch</span>
            <span class="fw-bold">{{ count($group['batches']) }}</span>
          </div>
        </div>
        <hr>
        @foreach($group['batches'] as $gb)
          @php
            $gbColor = match($gb['indicator']) {
              'expired' => '#DC3545',
              'kritis' => '#FD7E14',
              'waspada' => '#FFAE1F',
              default => '#39B69A',
            };
            $gbLabel = match($gb['indicator']) {
              'expired' => 'Expired',
              'kritis' => 'Kritis',
              'waspada' => 'Waspada',
              default => 'Normal',
            };
          @endphp
          <div class="border rounded p-3 mb-2" style="border-left: 4px solid {{ $gbColor }} !important;">
            <div class="d-flex justify-content-between align-items-center">
              <div>
                <strong><code>{{ $gb['batch_kode'] }}</code></strong>
                <span class="badge bg-secondary ms-2">#{{ $gb['prioritas'] }}</span>
              </div>
              <span class="badge" style="background:{{ $gbColor }};color:#fff;">{{ $gbLabel }}</span>
            </div>
            <div class="d-flex justify-content-between mt-2 small">
              <span>Masuk: {{ $gb['tgl_masuk'] }}</span>
              <span>Kadaluarsa: {{ $gb['tgl_kadaluarsa'] }}</span>
            </div>
            <div class="d-flex justify-content-between mt-1 small">
              <span>Sisa Stok: <strong>{{ number_format($gb['sisa_stok'], 0) }} {{ $group['satuan'] }}</strong></span>
              <span>Sisa Umur: <strong>{{ $gb['sisa_umur'] }}</strong></span>
            </div>
          </div>
        @endforeach
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
      </div>
    </div>
  </div>
</div>
@endforeach

@endsection