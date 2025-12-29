<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Modernize Free')</title>
  <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/appLogo.png') }}" />
  <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
            <img src="{{ asset('assets/images/logos/appLogo.png') }}" width="180" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-8"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Home</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('dashboard') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-layout-dashboard"></i>
                </span>
                <span class="hide-menu">Dashboard</span>
              </a>
            </li>

            {{-- Menu --}}
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Menu</span>
            </li>

            @php
              $user = Auth::user();
              $isAdmin = $user && $user->isAdmin();
              $isWarehouseStaff = $user && $user->isWarehouseStaff();
              $isQcStaff = $user && $user->isQcStaff();
            @endphp

            {{-- Bahan Baku - Admin & Warehouse Staff --}}
            @if($isAdmin || $isWarehouseStaff)
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('raw_stock.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-article"></i>
                </span>
                <span class="hide-menu">Bahan Baku</span>
              </a>
            </li>
            @endif

            {{-- Produksi - Admin & Warehouse Staff --}}
            @if($isAdmin || $isWarehouseStaff)
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('production.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-cards"></i>
                </span>
                <span class="hide-menu">Produksi</span>
              </a>
            </li>
            @endif

            {{-- Detail Produk - Admin only --}}
            @if($isAdmin)
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('detail_product.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-package"></i>
                </span>
                <span class="hide-menu">Detail Produk</span>
              </a>
            </li>
            @endif

            {{-- Quality Control - Admin & QC Staff --}}
            @if($isAdmin || $isQcStaff)
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('qc_check.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-checklist"></i>
                </span>
                <span class="hide-menu">Quality Control</span>
              </a>
            </li>
            @endif
            
            {{-- Transaksi - Admin only --}}
            @if($isAdmin)
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('transactions.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-credit-card"></i>
                </span>
                <span class="hide-menu">Transaksi</span>
              </a>
            </li>
            @endif

            {{-- Admin Only Menu --}}
            @if($isAdmin)
            <li class="nav-small-cap">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Administrasi</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('users.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-users"></i>
                </span>
                <span class="hide-menu">Kelola Akun</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link" href="{{ route('tasks.index') }}" aria-expanded="false">
                <span>
                  <i class="ti ti-clipboard-list"></i>
                </span>
                <span class="hide-menu">Kelola Task</span>
              </a>
            </li>
            @endif

            {{-- Logout Button at Bottom --}}
            <li class="nav-small-cap mt-5">
              <i class="ti ti-dots nav-small-cap-icon fs-4"></i>
              <span class="hide-menu">Akun</span>
            </li>
            <li class="sidebar-item">
              <form action="{{ route('logout') }}" method="POST" class="d-block">
                @csrf
                <button type="submit" class="sidebar-link btn btn-link text-start w-100" style="border: none; background: none; padding: 10px 0; text-decoration: none;">
                  <span>
                    <i class="ti ti-logout"></i>
                  </span>
                  <span class="hide-menu">Logout</span>
                </button>
              </form>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->
    <!--  Main wrapper -->
    <div class="body-wrapper">
      <!--  Header Start -->
      <header class="app-header">
        <nav class="navbar navbar-expand-lg navbar-light">
          <ul class="navbar-nav">
            <li class="nav-item d-block d-xl-none">
              <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse" href="javascript:void(0)" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="ti ti-menu-2"></i>
              </a>
              <div class="dropdown-menu dropdown-menu-start" style="min-width: 250px;">
                <div class="px-3 py-2">
                  <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="40" height="40" class="rounded-circle">
                    <div>
                      <p class="mb-0 fw-semibold">{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</p>
                      <small class="text-muted">
                        @if(Auth::user()->isAdmin())
                          Administrator
                        @elseif(Auth::user()->isWarehouseStaff())
                          Warehouse Staff
                        @elseif(Auth::user()->isQcStaff())
                          QC Staff
                        @endif
                      </small>
                    </div>
                  </div>
                  <hr class="my-2">
                  <a href="{{ route('users.profile') }}" class="d-block text-decoration-none text-dark mb-2">
                    <i class="ti ti-user me-2"></i>Lihat Profil
                  </a>
                </div>
              </div>
            </li>
            <li class="nav-item">
              <a class="nav-link nav-icon-hover position-relative" href="{{ route('notifications.index') }}" id="notificationLink">
                <i class="ti ti-bell-ringing"></i>
                <div class="notification bg-primary rounded-circle position-absolute top-0 start-100 translate-middle" id="notificationBadge" style="display: none; width: 8px; height: 8px;"></div>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notificationCount" style="display: none; font-size: 0.7rem; min-width: 18px; height: 18px; line-height: 18px;">0</span>
              </a>
            </li>
          </ul>
          <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
            <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
              <li class="nav-item dropdown">
                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                  aria-expanded="false">
                  <img src="{{ asset('assets/images/profile/user-1.jpg') }}" alt="" width="35" height="35" class="rounded-circle">
                </a>
                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
                  <div class="message-body">
                    <a href="{{ route('users.profile') }}" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-user fs-6"></i>
                      <p class="mb-0 fs-3">My Profile</p>
                    </a>
                    @if(!Auth::user()->isAdmin())
                    <a href="{{ route('tasks.index') }}" class="d-flex align-items-center gap-2 dropdown-item">
                      <i class="ti ti-list-check fs-6"></i>
                      <p class="mb-0 fs-3">My Task</p>
                    </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                      @csrf
                      <button type="submit" class="btn btn-outline-primary mx-3 mt-2 d-block w-100">
                        <i class="ti ti-logout"></i> Logout
                      </button>
                    </form>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </nav>
      </header>
      <!--  Header End -->
      <div class="container-fluid b">
        @yield('content')
      </div>
    </div>
  </div>
  <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
  <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
  <script src="{{ asset('assets/js/app.min.js') }}"></script>
  <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
  <script src="{{ asset('assets/js/dashboard.js') }}"></script>
  @stack('scripts')
  <style>
    /* Global Card Styling - Visible Borders and Hover Effects */
    .card {
      border: 1px solid #e0e0e0 !important;
      transition: all 0.3s ease;
    }
    
    .card:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
      border-color: #0d6efd !important;
      transform: translateY(-2px);
    }
    
    .card.border-0 {
      border: 1px solid #e0e0e0 !important;
    }
    
    .card.shadow-sm:hover {
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }
  </style>
  <script>
    // Load notification count on page load
    document.addEventListener('DOMContentLoaded', function() {
      fetch('{{ route("notifications.count") }}')
        .then(response => response.json())
        .then(data => {
          const badge = document.getElementById('notificationBadge');
          const count = document.getElementById('notificationCount');
          if (data.count > 0) {
            badge.style.display = 'block';
            count.textContent = data.count > 99 ? '99+' : data.count;
            count.style.display = 'block';
          }
        })
        .catch(error => console.error('Error loading notifications:', error));
    });
  </script>
</body>

</html>