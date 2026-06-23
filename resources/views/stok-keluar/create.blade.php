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

        @if($bahanBakuId && ($availableBatches->isNotEmpty() || $expiredBatches->isNotEmpty()))
          <form action="{{ route('stok-keluar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="bahan_baku_id" value="{{ $bahanBakuId }}">

            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label">Jumlah Keluar</label>
                <input type="number" step="0.01" name="jumlah_keluar" class="form-control" placeholder="0" required
                       max="{{ $availableBatches->sum('sisa_stok') }}">
                <small class="text-muted">
                  Stok tersedia: <strong>{{ number_format($availableBatches->sum('sisa_stok'), 2) }}</strong>
                  @if($expiredBatches->isNotEmpty())
                    <span class="text-danger ms-2">
                      <i class="ti ti-alert-triangle me-1"></i>{{ number_format($expiredBatches->sum('sisa_stok'), 0) }} expired tidak terpakai
                    </span>
                  @endif
                </small>
              </div>
              <div class="col-md-6">
                <label class="form-label">Tanggal Keluar</label>
                <input type="date" name="tanggal_keluar" class="form-control" value="{{ date('Y-m-d') }}" required>
              </div>
            </div>

            @if($availableBatches->isNotEmpty())
            <div class="mb-3">
              <label class="form-label fw-medium">
                <i class="ti ti-package me-1 text-success"></i>Batch Tersedia
                <span class="badge bg-success ms-1">{{ $availableBatches->count() }}</span>
              </label>
              <small class="text-muted d-block mb-2">
                Sistem akan mengambil stok dari batch tertua secara otomatis (FIFO). Batch expired otomatis dilewati.
              </small>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0">
                  <thead class="table-light">
                    <tr>
                      <th>Batch</th>
                      <th>Tanggal Masuk</th>
                      <th>Sisa Stok</th>
                      <th>Sisa Umur</th>
                      <th>Indikator</th>
                    </tr>
                  </thead>
                  <tbody>
                    @php $now = now(); @endphp
                    @foreach($availableBatches as $batch)
                      @php
                        $bahan = $batch->bahanBaku;
                        $expiryDay = $bahan->hari_kedaluwarsa ?? 999;
                        $tglKadaluwarsa = $batch->tanggal_masuk->copy()->addDays($expiryDay);
                        $sisaHari = (int) $now->diffInDays($tglKadaluwarsa, false);
                        $pct = $expiryDay > 0 ? (($expiryDay - max(0, $sisaHari)) / $expiryDay) * 100 : 0;

                        if ($sisaHari <= 0) { $lvl = 'expired'; $label = 'Expired'; $icon = 'ti ti-alert-triangle'; $bg = 'bg-danger'; }
                        elseif ($pct > 75) { $lvl = 'kritis'; $label = 'Kritis'; $icon = 'ti ti-flame'; $bg = 'bg-kritis'; }
                        elseif ($pct > 50) { $lvl = 'waspada'; $label = 'Waspada'; $icon = 'ti ti-clock'; $bg = 'bg-warning text-dark'; }
                        else { $lvl = 'aman'; $label = 'Normal'; $icon = 'ti ti-check'; $bg = 'bg-success'; }
                      @endphp
                      <tr>
                        <td><code>{{ $batch->batch_kode }}</code></td>
                        <td>{{ $batch->tanggal_masuk->format('d M Y') }}</td>
                        <td><span class="fw-bold">{{ number_format($batch->sisa_stok, 2) }}</span></td>
                        <td>{{ $sisaHari > 0 ? $sisaHari . ' hr' : 'Expired' }}</td>
                        <td>
                          <span class="badge {{ $bg }}">
                            <i class="{{ $icon }} me-1" style="font-size:.75rem;"></i>{{ $label }}
                          </span>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif

            @if($expiredBatches->isNotEmpty())
            <div class="mb-3">
              <label class="form-label fw-medium">
                <i class="ti ti-clock-off me-1 text-danger"></i>Expired (tidak dapat digunakan)
                <span class="badge bg-danger ms-1">{{ $expiredBatches->count() }}</span>
              </label>
              <div class="table-responsive">
                <table class="table table-sm table-bordered mb-0" style="opacity:0.7;">
                  <thead class="table-danger">
                    <tr>
                      <th>Batch</th>
                      <th>Tanggal Masuk</th>
                      <th>Sisa Stok</th>
                      <th>Kadaluarsa</th>
                      <th>Indikator</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($expiredBatches as $batch)
                      @php
                        $tglExp = $batch->tanggal_masuk->addDays($batch->bahanBaku->hari_kedaluwarsa);
                      @endphp
                      <tr>
                        <td><code>{{ $batch->batch_kode }}</code></td>
                        <td>{{ $batch->tanggal_masuk->format('d M Y') }}</td>
                        <td><span class="fw-bold">{{ number_format($batch->sisa_stok, 2) }}</span></td>
                        <td class="text-danger">{{ $tglExp->format('d M Y') }}</td>
                        <td>
                          <span class="badge bg-danger">
                            <i class="ti ti-alert-triangle me-1" style="font-size:.75rem;"></i>Expired
                          </span>
                        </td>
                        <td>
                          <form action="{{ route('fifo-monitoring.destroy', $batch->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin membuang batch {{ $batch->batch_kode }} ({{ number_format($batch->sisa_stok, 0) }})?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">
                              <i class="ti ti-trash me-1"></i>Buang
                            </button>
                          </form>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
            @endif

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
        @elseif($bahanBakuId && $availableBatches->isEmpty() && $expiredBatches->isEmpty())
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
