<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Sego Sambel Merdeka') - Sistem Inventory</title>
  <link rel="shortcut icon" type="image/png" href="../assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="../assets/css/styles.min.css" />
  <style>
    .profile-dropdown .dropdown-menu { min-width: 200px; }
    @media (max-width: 768px) {
      .profile-dropdown { position: static !important; text-align: right; padding: 10px; }
    }

    .app-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1030;
      background: #fff;
      box-shadow: 0 2px 4px rgba(0,0,0,.08);
      height: 64px;
    }

    .hamburger-btn {
      background: none;
      border: none;
      cursor: pointer;
      padding: 8px 12px;
      border-radius: 8px;
      transition: background .15s;
    }
    .hamburger-btn:hover { background: #f1f1f1; }
    .hamburger-btn svg { display: block; }

    #main-wrapper[data-sidebartype="mini-sidebar"] .body-wrapper { margin-left: 0 !important; }
    #main-wrapper[data-sidebartype="mini-sidebar"].show-sidebar .body-wrapper { margin-left: 0 !important; }

    #main-wrapper[data-sidebartype="mini-sidebar"] .left-sidebar {
      left: -270px;
      transition: left .2s ease;
      z-index: 1040 !important;
    }

    #main-wrapper[data-sidebartype="mini-sidebar"].show-sidebar .left-sidebar {
      left: 0;
      transition: left .2s ease;
      box-shadow: 2px 0 8px rgba(0,0,0,.15);
    }

    .sidebar-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,.35);
      z-index: 1035;
    }
    #main-wrapper.show-sidebar .sidebar-overlay { display: block; }

    .body-wrapper .container-fluid,
    .body-wrapper .container-sm,
    .body-wrapper .container-md,
    .body-wrapper .container-lg,
    .body-wrapper .container-xl,
    .body-wrapper .container-xxl {
      padding-top: 84px !important;
    }

    .sidebar-nav .nav-small-cap .nav-small-cap-icon {
      font-size: 1rem;
    }

    .sidebar-link {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 16px;
      color: #5A6A7E;
      text-decoration: none;
      border-radius: 8px;
      margin: 2px 8px;
      transition: all .15s;
    }
    .sidebar-link:hover {
      background: #f0f5ff;
      color: #5D87FF;
    }
    .sidebar-link.active {
      background: #5D87FF;
      color: #fff;
    }
    .sidebar-link.active i,
    .sidebar-link.active iconify-icon {
      color: #fff !important;
    }
    .sidebar-link i,
    .sidebar-link iconify-icon {
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .sidebar-item .badge {
      margin-left: auto;
    }

    .nav-small-cap {
      padding: 8px 16px 4px;
      font-size: .75rem;
      text-transform: uppercase;
      letter-spacing: .5px;
      color: #adb5bd;
      font-weight: 600;
    }

    .sidebar-divider {
      height: 1px;
      background: #e9ecef;
      margin: 8px 16px;
    }

    .role-badge {
      font-size: .7rem;
      padding: 2px 10px;
      border-radius: 20px;
      font-weight: 500;
    }

    .stat-card {
      border: none;
      border-radius: 12px;
      transition: transform .15s, box-shadow .15s;
    }
    .stat-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 24px rgba(0,0,0,.08);
    }
    .stat-card .card-icon {
      width: 48px;
      height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.5rem;
    }

    .table th {
      font-weight: 600;
      font-size: .8rem;
      text-transform: uppercase;
      letter-spacing: .3px;
      color: #6c757d;
    }

    .page-title {
      font-size: 1.25rem;
      font-weight: 600;
      color: #2A3547;
    }

    .sidebar-logout {
      margin-top: auto;
      padding: 12px 16px;
      border-top: 1px solid #e9ecef;
    }
    .sidebar-logout form {
      margin: 0;
    }
    .sidebar-logout .sidebar-link {
      margin: 0;
    }

    .left-sidebar {
      display: flex;
      flex-direction: column;
    }
    .sidebar-nav.scroll-sidebar {
      flex: 1;
      overflow-y: auto;
    }
  </style>
  @stack('styles')
</head>

<body>
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6"
       data-sidebartype="mini-sidebar" data-sidebar-position="fixed" data-header-position="fixed">

    <header class="app-header d-flex align-items-center px-3">
      <button class="hamburger-btn sidebartoggler" id="sidebarToggle" aria-label="Toggle sidebar">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#333" stroke-width="2"
             stroke-linecap="round" stroke-linejoin="round">
          <line x1="3" y1="6" x2="21" y2="6"></line>
          <line x1="3" y1="12" x2="21" y2="12"></line>
          <line x1="3" y1="18" x2="21" y2="18"></line>
        </svg>
      </button>

      <div class="ms-3">
        <span class="page-title">@yield('title', 'Dashboard')</span>
      </div>

      <div class="ms-auto">
        <div class="profile-dropdown position-relative" style="z-index: 1050;">
          <div class="dropdown">
            <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button"
                    id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <img src="../assets/images/profile/user1.jpg" alt="Profile" class="rounded-circle me-2" width="36" height="36">
              <span>{{ auth()->user()->username }}</span>
              @auth
                @php
                  $roleLabel = match(auth()->user()->role) {
                    'admin' => 'Admin',
                    'karyawan' => 'Karyawan',
                    'owner' => 'Owner',
                    default => 'User'
                  };
                  $roleClass = match(auth()->user()->role) {
                    'admin' => 'bg-danger',
                    'karyawan' => 'bg-primary',
                    'owner' => 'bg-success',
                    default => 'bg-secondary'
                  };
                @endphp
                <span class="role-badge {{ $roleClass }} ms-2">{{ $roleLabel }}</span>
              @endauth
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
              <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                <i class="ti ti-user me-2"></i>My Profile</a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="dropdown-item">
                    <i class="ti ti-logout me-2"></i>Logout
                  </button>
                </form>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </header>

    <div class="sidebar-overlay sidebartoggler"></div>

    <aside class="left-sidebar">
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between px-3 pt-3">
          <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
            <img src="../assets/images/logos/SuperStock.png" alt="Sego Sambel Merdeka" />
          </a>
          <div class="close-btn d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>

        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          @include('layouts.partials.sidebar')
        </nav>

        <div class="sidebar-logout">
          <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start" style="color:#DC3545;">
              <i class="ti ti-logout" style="color:#DC3545;"></i>
              <span class="hide-menu">Logout</span>
            </button>
          </form>
        </div>
      </div>
    </aside>

    <div class="body-wrapper">
      <div class="body-wrapper-inner">
        <div class="container-fluid">

          @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="ti ti-check-circle fs-5 me-2"></i>
              {{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
              <i class="ti ti-alert-circle fs-5 me-2"></i>
              {{ session('error') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
          @endif

          @yield('content')

          <div class="py-4 px-6 text-center">
            <p class="mb-0 fs-4">
              Sistem Pengelolaan Stok Bahan Baku UMKM Sego Sambel Merdeka
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../assets/libs/jquery/dist/jquery.min.js"></script>
  <script src="../assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/sidebarmenu.js"></script>
  <script src="../assets/libs/apexcharts/dist/apexcharts.min.js"></script>
  <script src="../assets/libs/simplebar/dist/simplebar.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script>
  <script>
    $(document).ready(function() {
      $('.sidebartoggler').on('click', function() {
        $('#main-wrapper').toggleClass('show-sidebar');
      });
    });
  </script>
  @stack('scripts')
</body>
</html>
