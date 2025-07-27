<ul class="navbar-nav bg-white sidebar sidebar-light accordion shadow-sm" id="accordionSidebar">

    <a class="sidebar-brand d-flex align-items-center justify-content-center py-4" href="{{ route('dashboard') }}">
        <div class="sidebar-brand-icon text-primary">
            <i class="fas fa-utensils fa-2x"></i>
        </div>
        <div class="sidebar-brand-text mx-2">Canteen</div>
    </a>

    <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <a class="nav-link" href="{{ route('dashboard') }}">
            <i class="fas fa-fw fa-tachometer-alt text-primary"></i>
            <span class="ml-2">Dashboard</span>
        </a>
    </li>

    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Operasional & Master
    </div>

    {{-- Menu Khusus Admin --}}
    @if(auth()->user()->role == 'admin')
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-calendar-alt text-primary"></i>
                <span class="ml-2">Atur Jadwal Makan</span>
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMasterData">
                <i class="fas fa-fw fa-database text-primary"></i>
                <span class="ml-2">Kelola Master Data</span>
            </a>
            <div id="collapseMasterData" class="collapse" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                    <h6 class="collapse-header">Database Sistem:</h6>
                    <a class="collapse-item" href="#">Kelola Menu</a>
                    <a class="collapse-item" href="#">Kelola Karyawan</a>
                    <a class="collapse-item" href="#">Kelola Counter</a>
                </div>
            </div>
        </li>
    @endif
    
    <hr class="sidebar-divider">

    <div class="sidebar-heading">
        Laporan & Log
    </div>

    {{-- Menu untuk HR dan juga Admin --}}
    @if(in_array(auth()->user()->role, ['admin', 'hr']))
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-chart-bar text-primary"></i>
                <span class="ml-2">Laporan Konsumsi</span>
            </a>
        </li>
    @endif
    
    {{-- Menu Khusus Admin --}}
    @if(auth()->user()->role == 'admin')
        <li class="nav-item">
            <a class="nav-link" href="#">
                <i class="fas fa-fw fa-history text-primary"></i>
                <span class="ml-2">Log Transaksi</span>
            </a>
        </li>
    @endif

    <hr class="sidebar-divider d-none d-md-block">

    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0 bg-light" id="sidebarToggle"></button>
    </div>
</ul>