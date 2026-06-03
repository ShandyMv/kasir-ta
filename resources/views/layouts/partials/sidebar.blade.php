@php
  $user = auth()->user();
  $currentRoute = request()->route() ? request()->route()->getName() : '';
  $routeStartsWith = fn($prefix) => str_starts_with($currentRoute, $prefix);
@endphp

<ul id="sidebarnav">
  <li class="nav-small-cap">
    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
    <span class="hide-menu">Menu Utama</span>
  </li>

  <li class="sidebar-item">
    <a class="sidebar-link {{ $currentRoute === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
      <i class="ti ti-layout-dashboard"></i>
      <span class="hide-menu">Dashboard</span>
    </a>
  </li>

  @if($user->isAdmin() || $user->isKaryawan())
    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Inventory</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('stok-masuk') ? 'active' : '' }}" href="{{ route('stok-masuk') }}">
        <i class="ti ti-archive-down"></i>
        <span class="hide-menu">Stok Masuk</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('stok-keluar') ? 'active' : '' }}" href="{{ route('stok-keluar') }}">
        <i class="ti ti-archive-up"></i>
        <span class="hide-menu">Stok Keluar</span>
      </a>
    </li>
  @endif

  <li class="nav-small-cap">
    <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
    <span class="hide-menu">Monitoring</span>
  </li>

  <li class="sidebar-item">
    <a class="sidebar-link {{ $currentRoute === 'fifo-monitoring' ? 'active' : '' }}" href="{{ route('fifo-monitoring') }}">
      <i class="ti ti-eye"></i>
      <span class="hide-menu">FIFO Monitoring</span>
    </a>
  </li>

  @if($user->isAdmin())
    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Administrasi</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('user-management') ? 'active' : '' }}" href="{{ route('user-management') }}">
        <i class="ti ti-users"></i>
        <span class="hide-menu">User Management</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('supplier') ? 'active' : '' }}" href="{{ route('supplier') }}">
        <i class="ti ti-building-store"></i>
        <span class="hide-menu">Supplier</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('satuan') ? 'active' : '' }}" href="{{ route('satuan') }}">
        <i class="ti ti-ruler"></i>
        <span class="hide-menu">Satuan</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('bahan-baku') ? 'active' : '' }}" href="{{ route('bahan-baku') }}">
        <i class="ti ti-box"></i>
        <span class="hide-menu">Bahan Baku</span>
      </a>
    </li>
  @endif

  @if($user->isAdmin() || $user->isOwner())
    <li class="nav-small-cap">
      <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
      <span class="hide-menu">Analisis</span>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $currentRoute === 'min-max-analysis' ? 'active' : '' }}" href="{{ route('min-max-analysis') }}">
        <i class="ti ti-chart-bar"></i>
        <span class="hide-menu">Min-Max Analysis</span>
      </a>
    </li>

    <li class="sidebar-item">
      <a class="sidebar-link {{ $routeStartsWith('laporan') ? 'active' : '' }}" href="{{ route('laporan') }}">
        <i class="ti ti-file-text"></i>
        <span class="hide-menu">Laporan</span>
      </a>
    </li>
  @endif
</ul>
