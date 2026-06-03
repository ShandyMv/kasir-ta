@extends('layouts.admin')

@section('title', 'User Management')

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
      <h5 class="card-title fw-semibold mb-0">
        <i class="ti ti-users me-2 text-primary"></i>Daftar User
      </h5>
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahUserModal">
        <i class="ti ti-plus me-1"></i>Tambah User
      </button>
    </div>

    <form method="GET" class="d-flex flex-wrap align-items-center gap-3 mb-3">
      <div class="input-group" style="max-width:300px;">
        <span class="input-group-text bg-transparent"><i class="ti ti-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Cari user..." value="{{ $search }}">
      </div>
      <button type="submit" class="btn btn-outline-primary btn-sm">Cari</button>
      @if($search)
        <a href="{{ route('user-management') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
      @endif
    </form>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $i => $user)
          <tr>
            <td>{{ $users->firstItem() + $i }}</td>
            <td class="fw-medium">{{ $user->name }}</td>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>
              @php
                $roleBadge = match($user->role) {
                  'admin' => 'bg-danger',
                  'karyawan' => 'bg-primary',
                  'owner' => 'bg-success',
                  default => 'bg-secondary'
                };
                $roleLabel = match($user->role) {
                  'admin' => 'Admin',
                  'karyawan' => 'Karyawan',
                  'owner' => 'Owner',
                  default => $user->role
                };
              @endphp
              <span class="badge {{ $roleBadge }}">{{ $roleLabel }}</span>
            </td>
            <td>
              <div class="btn-group">
                <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                        data-bs-target="#editUserModal{{ $user->id }}" title="Edit">
                  <i class="ti ti-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal"
                        data-bs-target="#hapusUserModal{{ $user->id }}" title="Hapus">
                  <i class="ti ti-trash"></i>
                </button>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="6" class="text-center text-muted py-4">Tidak ada user ditemukan.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">
      {{ $users->appends(request()->query())->links() }}
    </div>
  </div>
</div>

<div class="modal fade" id="tambahUserModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('user-management.store') }}" method="POST">
        @csrf
        <div class="modal-header border-0">
          <h5 class="modal-title fw-semibold">
            <i class="ti ti-user-plus me-2 text-primary"></i>Tambah User
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name', 'store') is-invalid @enderror" value="{{ old('name') }}" placeholder="Masukkan nama lengkap" required>
            @error('name', 'store')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control @error('username', 'store') is-invalid @enderror" value="{{ old('username') }}" placeholder="Masukkan username" required>
            @error('username', 'store')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email', 'store') is-invalid @enderror" value="{{ old('email') }}" placeholder="Masukkan email" required>
            @error('email', 'store')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password <span class="text-danger">*</span></label>
            <input type="password" name="password" class="form-control @error('password', 'store') is-invalid @enderror" placeholder="Minimal 4 karakter" required>
            @error('password', 'store')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role', 'store') is-invalid @enderror">
              <option value="">-- Pilih Role --</option>
              <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="karyawan" {{ old('role') === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
              <option value="owner" {{ old('role') === 'owner' ? 'selected' : '' }}>Owner</option>
            </select>
            @error('role', 'store')
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

@foreach($users as $user)
<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form action="{{ route('user-management.update', $user->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header border-0">
          <h5 class="modal-title fw-semibold">
            <i class="ti ti-user-edit me-2 text-warning"></i>Edit User
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control @error('name', 'update'.$user->id) is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
            @error('name', 'update'.$user->id)
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Username <span class="text-danger">*</span></label>
            <input type="text" name="username" class="form-control @error('username', 'update'.$user->id) is-invalid @enderror" value="{{ old('username', $user->username) }}" required>
            @error('username', 'update'.$user->id)
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" name="email" class="form-control @error('email', 'update'.$user->id) is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
            @error('email', 'update'.$user->id)
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Password <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
            <input type="password" name="password" class="form-control @error('password', 'update'.$user->id) is-invalid @enderror" placeholder="Minimal 4 karakter">
            @error('password', 'update'.$user->id)
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Role <span class="text-danger">*</span></label>
            <select name="role" class="form-select @error('role', 'update'.$user->id) is-invalid @enderror">
              <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin</option>
              <option value="karyawan" {{ old('role', $user->role) === 'karyawan' ? 'selected' : '' }}>Karyawan</option>
              <option value="owner" {{ old('role', $user->role) === 'owner' ? 'selected' : '' }}>Owner</option>
            </select>
            @error('role', 'update'.$user->id)
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

<div class="modal fade" id="hapusUserModal{{ $user->id }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <form action="{{ route('user-management.destroy', $user->id) }}" method="POST">
        @csrf
        @method('DELETE')
        <div class="modal-body text-center py-4">
          <i class="ti ti-alert-triangle text-danger fs-1 mb-3 d-block"></i>
          <h5 class="fw-semibold">Hapus User</h5>
          <p class="text-muted mb-0">Apakah Anda yakin ingin menghapus user<br>
            <strong>{{ $user->name }}</strong> ({{ $user->username }})?</p>
        </div>
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
