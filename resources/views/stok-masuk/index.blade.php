@php
$totalTransaksi = $stokMasuks->total();
$totalJumlah = $stokMasuks->sum('jumlah');
@endphp

@extends('layouts.admin')

@section('title', 'Stok Masuk')

@section('content')
<div class="row mb-4">
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-archive-down" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi, 0) }}</h3>
          <small class="text-muted">Total Transaksi Masuk</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-info bg-opacity-10 me-3" style="color:#46CA94;">
          <i class="ti ti-box" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($totalJumlah, 2) }}</h3>
          <small class="text-muted">Total Jumlah Masuk</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-primary bg-opacity-10 me-3" style="color:#5D87FF;">
          <i class="ti ti-building-store" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($suppliers->count(), 0) }} Supplier</h3>
          <small class="text-muted">Supplier Aktif</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-login me-2 text-success"></i>Riwayat Stok Masuk
      </h5>
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#tambahStokMasukModal">
        <i class="ti ti-plus me-1"></i>Tambah Stok Masuk
      </button>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:250px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan/batch..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('stok-masuk') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Bahan Baku</th>
            <th>Supplier</th>
            <th>Jumlah</th>
            <th>Batch</th>
            <th>Keterangan</th>
            <th>Input Oleh</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stokMasuks as $i => $sm)
          <tr>
            <td>{{ $stokMasuks->firstItem() + $i }}</td>
            <td><span class="fw-medium">{{ $sm->tanggal_masuk->format('d M Y') }}</span></td>
            <td>{{ $sm->bahanBaku->nama_bahan }}</td>
            <td>{{ $sm->supplier->nama_supplier }}</td>
            <td>
              <span class="fw-bold">{{ number_format($sm->jumlah, 2) }}</span>
              <small class="text-muted">{{ $sm->bahanBaku->satuan->nama_satuan }}</small>
            </td>
            <td><code>{{ $sm->batch_kode }}</code></td>
            <td><small class="text-muted">{{ $sm->keterangan ?? '-' }}</small></td>
            <td>
              <span class="badge bg-light text-dark">
                <i class="ti ti-user me-1"></i>{{ $sm->user->name }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted py-4">Belum ada transaksi stok masuk.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $stokMasuks->appends(request()->query())->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="tambahStokMasukModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-login me-2 text-success"></i>Tambah Stok Masuk
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('stok-masuk.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Bahan Baku <span class="text-danger">*</span></label>
            <select name="bahan_baku_id" class="form-select @error('bahan_baku_id') is-invalid @enderror" required>
              <option value="">-- Pilih Bahan --</option>
              @foreach($bahanBakus as $b)
                <option value="{{ $b->id }}" {{ old('bahan_baku_id') == $b->id ? 'selected' : '' }}>{{ $b->nama_bahan }} ({{ $b->kode_bahan }})</option>
              @endforeach
            </select>
            @error('bahan_baku_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Supplier <span class="text-danger">*</span></label>
            <select name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror" required>
              <option value="">-- Pilih Supplier --</option>
              @foreach($suppliers as $sup)
                <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->nama_supplier }}</option>
              @endforeach
            </select>
            @error('supplier_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Jumlah <span class="text-danger">*</span></label>
              <input type="number" step="0.01" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror" placeholder="0" value="{{ old('jumlah') }}" required>
              @error('jumlah')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Tanggal <span class="text-danger">*</span></label>
              <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror" value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
              @error('tanggal_masuk')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Keterangan</label>
            <textarea name="keterangan" class="form-control @error('keterangan') is-invalid @enderror" rows="2" placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
            @error('keterangan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-success">
            <i class="ti ti-check me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
