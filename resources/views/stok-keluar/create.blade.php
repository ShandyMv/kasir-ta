@extends('layouts.admin')

@section('title', 'Input Stok Keluar')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h5 class="card-title fw-semibold mb-0">
            <i class="ti ti-logout me-2 text-warning"></i>Input Stok Keluar (FIFO)
          </h5>
          <a href="{{ route('stok-keluar') }}" class="btn btn-light btn-sm">
            <i class="ti ti-arrow-left me-1"></i>Kembali
          </a>
        </div>

        <form method="GET" action="{{ route('stok-keluar.create') }}" class="row g-3 mb-4">
          <div class="col-md-8">
            <label class="form-label">Pilih Bahan Baku</label>
            <select name="bahan_baku_id" class="form-select" onchange="this.form.submit()">
              <option value="">-- Pilih Bahan --</option>
              @foreach($bahanBakus as $b)
                <option value="{{ $b->id }}" {{ $bahanBakuId == $b->id ? 'selected' : '' }}>
                  {{ $b->nama_bahan }} ({{ $b->kode_bahan }}) - Stok: {{ number_format($b->stok_saat_ini, 0) }} {{ $b->satuan->nama_satuan }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4 d-flex align-items-end">
            @if($bahanBakuId)
              <a href="{{ route('stok-keluar.create') }}" class="btn btn-outline-secondary">Ganti</a>
            @endif
          </div>
        </form>

        @if($bahanBakuId && $batches->isNotEmpty())
          <form action="{{ route('stok-keluar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bahan_baku_id" value="{{ $bahanBakuId }}">

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Jumlah Keluar</label>
                <input type="number" step="0.01" name="jumlah_keluar" class="form-control" placeholder="0" required
                       max="{{ $batches->sum('sisa_stok') }}">
                <small class="text-muted">Maks: {{ number_format($batches->sum('sisa_stok'), 2) }}</small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>

            <div class="mb-4">
              <label class="form-label fw-medium">Batch FIFO Tersedia</label>
              <small class="text-muted d-block mb-2">
                Sistem akan mengambil stok dari batch tertua secara otomatis (FIFO).
              </small>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Batch</th>
                      <th>Tanggal Masuk</th>
                      <th>Sisa Stok</th>
                      <th>Usia</th>
                      <th>Prioritas</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($batches as $batch)
                      @php
                        $usia = (int) $batch->tanggal_masuk->diffInDays(now());
                        $level = $usia > 30 ? 'danger' : ($usia > 15 ? 'warning' : 'success');
                      @endphp
                      <tr>
                        <td><code>{{ $batch->batch_kode }}</code></td>
                        <td>{{ $batch->tanggal_masuk->format('d M Y') }}</td>
                        <td>
                          <span class="fw-bold">{{ number_format($batch->sisa_stok, 2) }}</span>
                        </td>
                        <td>{{ $usia }} hari</td>
                        <td>
                          @if($level === 'danger')
                            <span class="badge bg-danger"><i class="ti ti-alert-triangle me-1"></i>Prioritas!</span>
                          @elseif($level === 'warning')
                            <span class="badge bg-warning text-dark"><i class="ti ti-alert-circle me-1"></i>Segera</span>
                          @else
                            <span class="badge bg-success"><i class="ti ti-check me-1"></i>Normal</span>
                          @endif
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Keterangan</label>
              <textarea name="keterangan" class="form-control" rows="2" placeholder="Alasan penggunaan bahan (opsional)"></textarea>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-warning text-white">
                <i class="ti ti-check me-1"></i>Simpan Transaksi
              </button>
              <a href="{{ route('stok-keluar') }}" class="btn btn-light">Batal</a>
            </div>
          </form>
        @elseif($bahanBakuId && $batches->isEmpty())
          <div class="alert alert-info">
            <i class="ti ti-info-circle me-1"></i>Tidak ada batch dengan stok tersedia untuk bahan ini.
          </div>
        @else
          <div class="text-center text-muted py-5">
            <i class="ti ti-box fs-1 d-block mb-2"></i>
            Silakan pilih bahan baku terlebih dahulu.
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
