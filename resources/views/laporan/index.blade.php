@extends('layouts.admin')

@section('title', 'Laporan')

@section('content')
<div class="card mb-4">
  <div class="card-body">
    <h5 class="card-title fw-semibold mb-3">
      <i class="ti ti-filter me-2 text-primary"></i>Filter Laporan
    </h5>
    <form method="GET" class="row g-3 align-items-end">
      <div class="col-lg-2">
        <label class="form-label">Jenis Laporan</label>
        <select name="type" class="form-select" onchange="this.form.submit()">
          <option value="stok-masuk" {{ $type === 'stok-masuk' ? 'selected' : '' }}>Stok Masuk</option>
          <option value="stok-keluar" {{ $type === 'stok-keluar' ? 'selected' : '' }}>Stok Keluar</option>
          <option value="persediaan" {{ $type === 'persediaan' ? 'selected' : '' }}>Persediaan</option>
        </select>
      </div>
      @if($type !== 'persediaan')
      <div class="col-lg-2">
        <label class="form-label">Dari Tanggal</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="ti ti-calendar"></i></span>
          <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
      </div>
      <div class="col-lg-2">
        <label class="form-label">Sampai Tanggal</label>
        <div class="input-group">
          <span class="input-group-text bg-transparent"><i class="ti ti-calendar"></i></span>
          <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
      </div>
      @endif
      <div class="col-lg-2">
        <label class="form-label">Bahan Baku</label>
        <select name="bahan_baku_id" class="form-select">
          <option value="">Semua Bahan</option>
          @foreach($bahanBakus as $b)
            <option value="{{ $b->id }}" {{ $bahanBakuId == $b->id ? 'selected' : '' }}>{{ $b->nama_bahan }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-lg-2 d-flex gap-2">
        <button type="submit" class="btn btn-primary"><i class="ti ti-eye me-1"></i>Tampilkan</button>
        <a href="{{ route('laporan') }}" class="btn btn-outline-secondary"><i class="ti ti-refresh me-1"></i>Reset</a>
      </div>
    </form>
  </div>
</div>

@if($type !== 'persediaan')
<div class="row mb-4">
  <div class="col-lg-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-download" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($data->total(), 0) }}</h3>
          <small class="text-muted">Total Transaksi</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-info bg-opacity-10 me-3" style="color:#46CA94;">
          <i class="ti ti-box" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($total, 2) }}</h3>
          <small class="text-muted">Total {{ $type === 'stok-masuk' ? 'Masuk' : 'Keluar' }}</small>
        </div>
      </div>
    </div>
  </div>
</div>
@endif

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
      <h5 class="card-title fw-semibold mb-0">
        @if($type === 'stok-masuk')
          <i class="ti ti-login me-2 text-success"></i>Laporan Stok Masuk
        @elseif($type === 'stok-keluar')
          <i class="ti ti-logout me-2 text-warning"></i>Laporan Stok Keluar
        @else
          <i class="ti ti-clipboard-data me-2 text-info"></i>Laporan Persediaan
        @endif
      </h5>
      <div class="d-flex gap-2">
        <a href="{{ route('laporan.export-pdf', request()->query()) }}" class="btn btn-danger btn-sm">
          <i class="ti ti-file-text me-1"></i>PDF
        </a>
        <a href="{{ route('laporan.export-excel', request()->query()) }}" class="btn btn-success btn-sm">
          <i class="ti ti-file-spreadsheet me-1"></i>Excel
        </a>
      </div>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          @if($type === 'stok-masuk')
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Bahan Baku</th>
            <th>Supplier</th>
            <th>Jumlah</th>
            <th>Satuan</th>
            <th>Batch</th>
            <th>Input Oleh</th>
          </tr>
          @elseif($type === 'stok-keluar')
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Bahan Baku</th>
            <th>Jumlah Keluar</th>
            <th>Satuan</th>
            <th>Keterangan</th>
            <th>Input Oleh</th>
          </tr>
          @else
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th>Stok Saat Ini</th>
            <th>Min</th>
            <th>Max</th>
            <th>Status</th>
          </tr>
          @endif
        </thead>
        <tbody>
          @if($type === 'stok-masuk')
            @forelse($data as $i => $d)
            <tr>
              <td>{{ $data->firstItem() + $i }}</td>
              <td>{{ $d->tanggal_masuk->format('d M Y') }}</td>
              <td>{{ $d->bahanBaku->nama_bahan }}</td>
              <td><small>{{ $d->supplier->nama_supplier }}</small></td>
              <td class="fw-medium text-success">{{ number_format($d->jumlah, 2) }}</td>
              <td>{{ $d->bahanBaku->satuan->nama_satuan }}</td>
              <td><code>{{ $d->batch_kode }}</code></td>
              <td>{{ $d->user->name }}</td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
            @endforelse
          @elseif($type === 'stok-keluar')
            @forelse($data as $i => $d)
            <tr>
              <td>{{ $data->firstItem() + $i }}</td>
              <td>{{ $d->tanggal_keluar->format('d M Y') }}</td>
              <td>{{ $d->bahanBaku->nama_bahan }}</td>
              <td class="fw-medium text-warning">{{ number_format($d->jumlah_keluar, 2) }}</td>
              <td>{{ $d->bahanBaku->satuan->nama_satuan }}</td>
              <td><small>{{ $d->keterangan ?? '-' }}</small></td>
              <td>{{ $d->user->name }}</td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data.</td></tr>
            @endforelse
          @else
            @forelse($data as $i => $d)
              @php
                $statusStok = $d->stok_saat_ini > $d->stok_maksimum ? 'Berlebih' : ($d->stok_saat_ini < $d->stok_minimum ? 'Restock' : 'Aman');
                $classStok = $d->stok_saat_ini > $d->stok_maksimum ? 'dark' : ($d->stok_saat_ini < $d->stok_minimum ? 'danger' : 'success');
              @endphp
            <tr>
              <td>{{ $data->firstItem() + $i }}</td>
              <td><code>{{ $d->kode_bahan }}</code></td>
              <td class="fw-medium">{{ $d->nama_bahan }}</td>
              <td>{{ $d->satuan->nama_satuan }}</td>
              <td class="fw-bold">{{ number_format($d->stok_saat_ini, 0) }}</td>
              <td>{{ number_format($d->stok_minimum, 0) }}</td>
              <td>{{ number_format($d->stok_maksimum, 0) }}</td>
              <td>
                <span class="badge bg-{{ $classStok }} bg-opacity-10 text-{{ $classStok }} px-3 py-1">{{ $statusStok }}</span>
              </td>
            </tr>
            @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada data.</td></tr>
            @endforelse
          @endif
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $data->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
