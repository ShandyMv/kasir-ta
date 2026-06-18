@php
$totalBahan = $bahanBakus->total();
$stokAman = $bahanBakus->filter(fn($b) => $b->stok_saat_ini >= $b->stok_minimum && $b->stok_saat_ini <= $b->stok_maksimum)->count();
$stokRestock = $bahanBakus->filter(fn($b) => $b->stok_saat_ini < $b->stok_minimum)->count();
$stokBerlebih = $bahanBakus->filter(fn($b) => $b->stok_saat_ini > $b->stok_maksimum)->count();

function getStatusBahan($stok, $min, $max) {
    if ($stok > $max) return ['label' => 'Berlebih', 'class' => 'dark'];
    if ($stok < $min) return ['label' => 'Restock', 'class' => 'danger'];
    return ['label' => 'Aman', 'class' => 'success'];
}
@endphp

@extends('layouts.admin')

@section('title', 'Bahan Baku')

@section('content')
<div class="row mb-4">
  <div class="col-lg-4">
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
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-shield-check"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokAman }}</h3>
          <small class="text-muted">Stok Aman</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-4">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-alert-triangle"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $stokRestock + $stokBerlebih }}</h3>
          <small class="text-muted">Perlu Perhatian</small>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-package me-2 text-primary"></i>Daftar Bahan Baku
      </h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBahanModal">
        <i class="ti ti-plus me-1"></i>Tambah Bahan
      </button>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari bahan..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('bahan-baku') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Kode</th>
            <th>Nama Bahan</th>
            <th>Satuan</th>
            <th>Stok</th>
            <th>Min</th>
            <th>Max</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bahanBakus as $i => $b)
            @php $status = getStatusBahan($b->stok_saat_ini, $b->stok_minimum, $b->stok_maksimum); @endphp
          <tr>
            <td>{{ $bahanBakus->firstItem() + $i }}</td>
            <td><code>{{ $b->kode_bahan }}</code></td>
            <td class="fw-medium">{{ $b->nama_bahan }}</td>
            <td>{{ $b->satuan->nama_satuan }}</td>
            <td>
              <span class="fw-bold">{{ number_format($b->stok_saat_ini, 0) }}</span>
              <small class="text-muted">{{ $b->satuan->nama_satuan }}</small>
            </td>
            <td>{{ number_format($b->stok_minimum, 0) }}</td>
            <td>{{ number_format($b->stok_maksimum, 0) }}</td>
            <td>
              <span class="badge bg-{{ $status['class'] }} bg-opacity-10 text-{{ $status['class'] }} px-3 py-1">
                {{ $status['label'] }}
              </span>
            </td>
            <td>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#editBahanModal{{ $b->id }}" title="Edit">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#hapusBahanModal{{ $b->id }}" title="Hapus">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="9" class="text-center text-muted py-4">Belum ada data bahan baku.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $bahanBakus->appends(request()->query())->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="tambahBahanModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-package me-2 text-primary"></i>Tambah Bahan Baku
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('bahan-baku.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Kode Bahan</label>
              <input type="text" class="form-control" placeholder="Otomatis" disabled>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Satuan <span class="text-danger">*</span></label>
              <select name="satuan_id" class="form-select @error('satuan_id') is-invalid @enderror" required>
                <option value="">-- Pilih Satuan --</option>
                @foreach($satuans as $sat)
                  <option value="{{ $sat->id }}" {{ old('satuan_id') == $sat->id ? 'selected' : '' }}>{{ $sat->nama_satuan }}</option>
                @endforeach
              </select>
              @error('satuan_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
            <input type="text" name="nama_bahan" class="form-control @error('nama_bahan') is-invalid @enderror" placeholder="Masukkan nama bahan" value="{{ old('nama_bahan') }}" required>
            @error('nama_bahan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok Minimum <small class="text-muted fst-italic">(opsional)</small></label>
                    <input type="number" step="0.01" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" placeholder="Kosongi jika belum tahu" value="{{ old('stok_minimum') }}">
                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Stok Maksimum <small class="text-muted fst-italic">(opsional)</small></label>
                    <input type="number" step="0.01" name="stok_maksimum" class="form-control @error('stok_maksimum') is-invalid @enderror" placeholder="Kosongi jika belum tahu" value="{{ old('stok_maksimum') }}">
                    @error('stok_maksimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Lead Time (hari)</label>
                <input type="number" name="lead_time" class="form-control @error('lead_time') is-invalid @enderror" placeholder="Lama waktu pemesanan" value="{{ old('lead_time', 1) }}" min="1">
                @error('lead_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="ti ti-check me-1"></i>Simpan
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach($bahanBakus as $b)
<div class="modal fade" id="editBahanModal{{ $b->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-package me-2 text-warning"></i>Edit Bahan Baku
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('bahan-baku.update', $b->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label">Kode Bahan</label>
              <input type="text" class="form-control" value="{{ $b->kode_bahan }}" disabled>
            </div>
            <div class="col-md-6 mb-3">
              <label class="form-label">Satuan <span class="text-danger">*</span></label>
              <select name="satuan_id" class="form-select @error('satuan_id') is-invalid @enderror" required>
                @foreach($satuans as $sat)
                  <option value="{{ $sat->id }}" {{ $b->satuan_id == $sat->id ? 'selected' : '' }}>{{ $sat->nama_satuan }}</option>
                @endforeach
              </select>
              @error('satuan_id')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">Nama Bahan <span class="text-danger">*</span></label>
            <input type="text" name="nama_bahan" class="form-control @error('nama_bahan') is-invalid @enderror" value="{{ old('nama_bahan', $b->nama_bahan) }}" required>
            @error('nama_bahan')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Saat Ini <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="stok_saat_ini" class="form-control @error('stok_saat_ini') is-invalid @enderror" value="{{ old('stok_saat_ini', $b->stok_saat_ini) }}" required>
                    @error('stok_saat_ini')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Minimum <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="stok_minimum" class="form-control @error('stok_minimum') is-invalid @enderror" value="{{ old('stok_minimum', $b->stok_minimum) }}" required>
                    @error('stok_minimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Stok Maksimum <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="stok_maksimum" class="form-control @error('stok_maksimum') is-invalid @enderror" value="{{ old('stok_maksimum', $b->stok_maksimum) }}" required>
                    @error('stok_maksimum')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Lead Time (hari)</label>
                <input type="number" name="lead_time" class="form-control @error('lead_time') is-invalid @enderror" value="{{ old('lead_time', $b->lead_time) }}" min="1">
                @error('lead_time')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="modal-footer border-0">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-warning">
            <i class="ti ti-check me-1"></i>Update
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="hapusBahanModal{{ $b->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="ti ti-alert-triangle text-danger fs-1 mb-3 d-block"></i>
        <h5 class="fw-semibold">Hapus Bahan Baku</h5>
        <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus<br>
          <strong>{{ $b->nama_bahan }}</strong> ({{ $b->kode_bahan }})?</p>
      </div>
      <form action="{{ route('bahan-baku.destroy', $b->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-footer border-0 justify-content-center">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-danger">
            <i class="ti ti-trash me-1"></i>Hapus
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endsection
