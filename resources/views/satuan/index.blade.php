@extends('layouts.admin')

@section('title', 'Satuan')

@section('content')
<div class="row mb-4">
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-primary bg-opacity-10 me-3" style="color:#5D87FF;">
          <i class="ti ti-ruler"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalSatuan }}</h3>
          <small class="text-muted">Total Satuan</small>
        </div>
      </div>
    </div>
  </div>
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-success bg-opacity-10 me-3" style="color:#39B69A;">
          <i class="ti ti-box"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalBahan }}</h3>
          <small class="text-muted">Total Bahan Baku</small>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
          <h5 class="card-title fw-semibold mb-0">
            <i class="ti ti-ruler me-2" style="color:#20C997;"></i>Daftar Satuan
          </h5>
          <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSatuanModal">
            <i class="ti ti-plus me-1"></i>Tambah Satuan
          </button>
        </div>

        <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
          <div class="input-group" style="max-width:300px;">
            <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
            <input type="text" name="search" class="form-control" placeholder="Cari satuan..." value="{{ $search }}">
          </div>
          <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
          @if($search)
            <a href="{{ route('satuan') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
          @endif
        </form>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead>
              <tr>
                <th style="width:60px;">No</th>
                <th>Nama Satuan</th>
                <th style="width:150px;">Aksi</th>
              </tr>
            </thead>
            <tbody>
              @forelse($satuans as $i => $s)
              <tr>
                <td>{{ $satuans->firstItem() + $i }}</td>
                <td class="fw-medium">{{ $s->nama_satuan }}</td>
                <td>
                  <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                            data-bs-target="#editSatuanModal{{ $s->id }}" title="Edit">
                      <i class="ti ti-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                            data-bs-target="#hapusSatuanModal{{ $s->id }}" title="Hapus">
                      <i class="ti ti-trash"></i>
                    </button>
                  </div>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="3" class="text-center text-muted py-4">Belum ada data satuan.</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <div class="mt-3">
          {{ $satuans->appends(request()->query())->links() }}
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="tambahSatuanModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-ruler me-2 text-primary"></i>Tambah Satuan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('satuan.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Satuan <span class="text-danger">*</span></label>
            <input type="text" name="nama_satuan" class="form-control @error('nama_satuan') is-invalid @enderror" placeholder="Masukkan nama satuan" value="{{ old('nama_satuan') }}" required>
            @error('nama_satuan')
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

@foreach($satuans as $s)
<div class="modal fade" id="editSatuanModal{{ $s->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-ruler me-2 text-warning"></i>Edit Satuan
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('satuan.update', $s->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Satuan <span class="text-danger">*</span></label>
            <input type="text" name="nama_satuan" class="form-control @error('nama_satuan') is-invalid @enderror" value="{{ old('nama_satuan', $s->nama_satuan) }}" required>
            @error('nama_satuan')
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

<div class="modal fade" id="hapusSatuanModal{{ $s->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="ti ti-alert-triangle text-danger fs-1 mb-3 d-block"></i>
        <h5 class="fw-semibold">Hapus Satuan</h5>
        <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus<br>
          <strong>{{ $s->nama_satuan }}</strong>?</p>
      </div>
      <form action="{{ route('satuan.destroy', $s->id) }}" method="POST">
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
