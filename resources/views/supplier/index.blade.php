@extends('layouts.admin')

@section('title', 'Supplier')

@section('content')
<div class="row mb-4">
  <div class="col-lg-3 col-md-6">
    <div class="card stat-card">
      <div class="card-body d-flex align-items-center">
        <div class="card-icon bg-danger bg-opacity-10 me-3" style="color:#DC3545;">
          <i class="ti ti-building-store"></i>
        </div>
        <div>
          <h3 class="mb-0 fw-bold">{{ $totalSupplier }}</h3>
          <small class="text-muted">Total Supplier</small>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-building-store me-2 text-danger"></i>Daftar Supplier
      </h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahSupplierModal">
        <i class="ti ti-plus me-1"></i>Tambah Supplier
      </button>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari supplier..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('supplier') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Supplier</th>
            <th>Telepon</th>
            <th>Alamat</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($suppliers as $i => $s)
          <tr>
            <td>{{ $suppliers->firstItem() + $i }}</td>
            <td class="fw-medium">{{ $s->nama_supplier }}</td>
            <td>{{ $s->telepon ?? '-' }}</td>
            <td>{{ $s->alamat ?? '-' }}</td>
            <td>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#editSupplierModal{{ $s->id }}" title="Edit">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#hapusSupplierModal{{ $s->id }}" title="Hapus">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="5" class="text-center text-muted py-4">Belum ada data supplier.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $suppliers->appends(request()->query())->links() }}
    </div>
  </div>
</div>
<div class="modal fade" id="tambahSupplierModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-building-store me-2 text-danger"></i>Tambah Supplier
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('supplier.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
            <input type="text" name="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" placeholder="Masukkan nama supplier" value="{{ old('nama_supplier') }}" required>
            @error('nama_supplier')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" placeholder="Masukkan nomor telepon" value="{{ old('telepon') }}">
            @error('telepon')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" placeholder="Masukkan alamat">{{ old('alamat') }}</textarea>
            @error('alamat')
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

@foreach($suppliers as $s)
<div class="modal fade" id="editSupplierModal{{ $s->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title fw-semibold">
          <i class="ti ti-building-store me-2 text-warning"></i>Edit Supplier
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('supplier.update', $s->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
            <input type="text" name="nama_supplier" class="form-control @error('nama_supplier') is-invalid @enderror" value="{{ old('nama_supplier', $s->nama_supplier) }}" required>
            @error('nama_supplier')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Telepon</label>
            <input type="text" name="telepon" class="form-control @error('telepon') is-invalid @enderror" value="{{ old('telepon', $s->telepon) }}">
            @error('telepon')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Alamat</label>
            <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3">{{ old('alamat', $s->alamat) }}</textarea>
            @error('alamat')
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

<div class="modal fade" id="hapusSupplierModal{{ $s->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <div class="modal-body text-center py-4">
        <i class="ti ti-alert-triangle text-danger fs-1 mb-3 d-block"></i>
        <h5 class="fw-semibold">Hapus Supplier</h5>
        <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus<br>
          <strong>{{ $s->nama_supplier }}</strong>?</p>
      </div>
      <form action="{{ route('supplier.destroy', $s->id) }}" method="POST">
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
