@php
$totalTransaksi = $stokKeluars->total();
$totalJumlahKeluar = $stokKeluars->sum('jumlah_keluar');
@endphp

@extends('layouts.admin')

@section('title', 'Stok Keluar')

@section('content')
<div class="row mb-4">
  <div class="col-lg-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-warning bg-opacity-10 me-3" style="color:#FFAE1F;">
          <i class="ti ti-archive-up" style="font-size:1.8rem;"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ number_format($totalTransaksi, 0) }}</h3>
          <small class="text-muted">Total Transaksi Keluar</small>
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
          <h3 class="mb-0 fw-bold">{{ number_format($totalJumlahKeluar, 2) }}</h3>
          <small class="text-muted">Total Jumlah Keluar</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-logout me-2 text-warning"></i>Riwayat Stok Keluar
      </h5>
      <a href="{{ route('stok-keluar.create') }}" class="btn btn-warning text-white">
        <i class="ti ti-plus me-1"></i>Input Stok Keluar
      </a>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:250px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('stok-keluar') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Tanggal</th>
            <th>Bahan Baku</th>
            <th>Jumlah Keluar</th>
            <th>Batch FIFO</th>
            <th>Keterangan</th>
            <th>Input Oleh</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stokKeluars as $i => $sk)
          <tr>
            <td>{{ $stokKeluars->firstItem() + $i }}</td>
            <td><span class="fw-medium">{{ $sk->tanggal_keluar->format('d M Y') }}</span></td>
            <td>{{ $sk->bahanBaku->nama_bahan }}</td>
            <td>
              <span class="fw-bold text-warning">{{ number_format($sk->jumlah_keluar, 2) }}</span>
              <small class="text-muted">{{ $sk->bahanBaku->satuan->nama_satuan }}</small>
            </td>
            <td>
              @foreach($sk->details as $det)
                <code>{{ $det->fifoBatch->batch_kode }}</code>@if(!$loop->last), @endif
              @endforeach
            </td>
            <td><small class="text-muted">{{ $sk->keterangan ?? '-' }}</small></td>
            <td>
              <span class="badge bg-light text-dark">
                <i class="ti ti-user me-1"></i>{{ $sk->user->name }}
              </span>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted py-4">Belum ada transaksi stok keluar.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $stokKeluars->appends(request()->query())->links() }}
    </div>
  </div>
</div>
@endsection
