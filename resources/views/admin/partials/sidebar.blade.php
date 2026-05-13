{{-- Admin Sidebar --}}
<aside class="admin-sidebar" id="admin-sidebar">
    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-mountain"></i>
        </div>
        <div>
            <span class="sidebar-brand">Garkadala Outdoor</span>
            <span class="sidebar-subtitle">Admin Portal</span>
        </div>
    </div>

    {{-- NAVIGATION --}}
    <nav class="sidebar-nav">
        <a href="{{ route('admin.dashboard') }}" class="sidebar-link @yield('sidebar-dashboard')" id="nav-dashboard">
            <i class="fas fa-th-large"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('admin.inventory.index') }}" class="sidebar-link @yield('sidebar-inventaris')" id="nav-inventaris">
            <i class="fas fa-boxes-stacked"></i>
            <span>Inventaris</span>
        </a>
        <a href="{{ route('admin.transaksi.index') }}" class="sidebar-link @yield('sidebar-transaksi')" id="nav-transaksi">
            <i class="fas fa-receipt"></i>
            <span>Transaksi</span>
        </a>
        <a href="#" class="sidebar-link @yield('sidebar-pengguna')" id="nav-pengguna">
            <i class="fas fa-users"></i>
            <span>Pengguna</span>
        </a>
        <a href="#" class="sidebar-link @yield('sidebar-notifikasi')" id="nav-notifikasi">
            <i class="fas fa-bell"></i>
            <span>Notifikasi</span>
        </a>
        <a href="#" class="sidebar-link @yield('sidebar-pengiriman')" id="nav-pengiriman">
            <i class="fas fa-truck"></i>
            <span>Pengiriman</span>
        </a>
    </nav>

    {{-- ADMIN PROFILE (bottom) --}}
    <div class="sidebar-profile">
        <div class="sidebar-avatar">
            <span>{{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 1)) }}</span>
        </div>
        <div class="sidebar-profile-info">
            <span class="sidebar-profile-name">{{ Auth::user()->nama_lengkap ?? 'Administrator' }}</span>
            <span class="sidebar-profile-role">ADMINISTRATOR</span>
        </div>
    </div>
</aside>
