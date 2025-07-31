<!-- Sidebar -->
<ul class="navbar-nav bg-white sidebar sidebar-light accordion shadow-sm" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center py-4" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon text-primary">
            <i class="fas fa-utensils fa-2x"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Canteen</div>
    </a>

    <!-- Nav Item - Dashboard -->
    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt text-primary"></i>
            <span class="ml-2">Dashboard</span>
        </a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading Operasional -->
    <div class="sidebar-heading">
        Operasional
    </div>

    {{-- Menu yang bisa diakses Admin --}}
    @if(auth()->user()->role == 'admin')
    <li class="nav-item {{ request()->routeIs('move-menu.create') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('move-menu.create') }}">
            <i class="fas fa-fw fa-random text-primary"></i>
            <span class="ml-2">Pindah Menu</span>
        </a>
    </li>
    <li class="nav-item {{ request()->is('schedules*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('schedules.index') }}">
            <i class="fas fa-fw fa-calendar-alt text-primary"></i>
            <span class="ml-2">Jadwal Makan</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading Laporan & Log -->
    <div class="sidebar-heading">
        Laporan & Log
    </div>

    {{-- Menu untuk Admin, HR, dan Security Officer --}}
    @if(in_array(auth()->user()->role, ['admin', 'hr', 'security_officer']))
    <li class="nav-item {{ request()->is('logs*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('logs.index') }}">
            <i class="fas fa-fw fa-history text-primary"></i>
            <span class="ml-2">Log Transaksi</span>
        </a>
    </li>
    @endif

    {{-- Menu untuk Admin dan HR --}}
    @if(in_array(auth()->user()->role, ['admin', 'hr']))
    <li class="nav-item {{ request()->is('reports*') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('reports.consumption') }}">
            <i class="fas fa-fw fa-chart-bar text-primary"></i>
            <span class="ml-2">Laporan Konsumsi</span>
        </a>
    </li>
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Heading Pengaturan -->
    <div class="sidebar-heading">
        Pengaturan & Master Data
    </div>
    
    {{-- Menu Master Data (dengan hak akses berbeda per item) --}}
    <li class="nav-item {{ request()->is('users*','employees*','cards*','gates*','menus*') ? 'active' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMasterData">
            <i class="fas fa-fw fa-database text-primary"></i>
            <span class="ml-2">Kelola Master Data</span>
        </a>
        <div id="collapseMasterData" class="collapse {{ request()->is('users*','employees*','cards*','gates*','menus*') ? 'show' : '' }}" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                {{-- Hanya Admin --}}
                @if(auth()->user()->role == 'admin')
                    <a class="collapse-item {{ request()->is('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Manajemen User</a>
                    <a class="collapse-item {{ request()->is('menus*') ? 'active' : '' }}" href="{{ route('menus.index') }}">Kelola Menu</a>
                    <a class="collapse-item {{ request()->is('gates*') ? 'active' : '' }}" href="{{ route('gates.index') }}">Kelola Counter</a>
                @endif
                
                {{-- Admin & HR --}}
                @if(in_array(auth()->user()->role, ['admin', 'hr']))
                    <a class="collapse-item {{ request()->is('employees*') ? 'active' : '' }}" href="{{ route('employees.index') }}">Kelola Karyawan</a>
                @endif

                {{-- Admin & Security Officer --}}
                @if(in_array(auth()->user()->role, ['admin', 'security_officer']))
                    <a class="collapse-item {{ request()->is('cards*') ? 'active' : '' }}" href="{{ route('cards.index') }}">Manajemen Kartu</a>
                @endif
            </div>
        </div>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0 bg-light" id="sidebarToggle"></button>
    </div>
</ul>
