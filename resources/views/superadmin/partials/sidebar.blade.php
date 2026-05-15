{{-- SuperAdmin Sidebar --}}
<aside class="admin-sidebar sa-sidebar" id="admin-sidebar">
    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon sa-logo-icon">
            <i class="fas fa-mountain"></i>
        </div>
        <div>
            <span class="sidebar-brand">GKDL ADMIN</span>
            <span class="sidebar-subtitle">RENTAL OPERATION</span>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        <a href="{{ route('superadmin.dashboard') }}" class="sidebar-link @yield('sidebar-dashboard')" id="nav-sa-dashboard">
            <i class="fas fa-chart-pie"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('superadmin.laporan') }}" class="sidebar-link @yield('sidebar-laporan')" id="nav-sa-laporan">
            <i class="fas fa-file-lines"></i>
            <span>Laporan</span>
        </a>
    </nav>

    {{-- BOTTOM LINKS --}}
    <div class="sa-sidebar-bottom">
        <a href="{{ route('superadmin.pengaturan') }}" class="sidebar-link @yield('sidebar-pengaturan')" id="nav-sa-pengaturan">
            <i class="fas fa-gear"></i>
            <span>Pengaturan</span>
        </a>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit" class="sidebar-link sa-logout-link">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
