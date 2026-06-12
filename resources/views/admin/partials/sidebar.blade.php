{{-- Admin Sidebar --}}
<aside class="admin-sidebar" id="admin-sidebar">
    {{-- LOGO --}}
    <div class="sidebar-logo">
        <div class="sidebar-logo-icon">
            <i class="fas fa-mountain"></i>
        </div>
        <div>
            <span class="sidebar-brand">Admin Portal</span>
            <span class="sidebar-subtitle">Executive View</span>
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
        <a href="{{ route('admin.kategori.index') }}" class="sidebar-link @yield('sidebar-kategori')" id="nav-kategori">
            <i class="fas fa-tags"></i>
            <span>Kategori</span>
        </a>
        <a href="{{ route('admin.ulasan.index') }}" class="sidebar-link @yield('sidebar-ulasan')" id="nav-ulasan">
            <i class="fas fa-star"></i>
            <span>Ulasan</span>
        </a>
        <a href="{{ route('admin.transaksi.index') }}" class="sidebar-link @yield('sidebar-transaksi')" id="nav-transaksi">
            <i class="fas fa-receipt"></i>
            <span>Transaksi</span>
        </a>
        <a href="{{ route('admin.pengguna.index') }}" class="sidebar-link @yield('sidebar-pengguna')" id="nav-pengguna">
            <i class="fas fa-users"></i>
            <span>Pengguna</span>
        </a>
        <a href="{{ route('admin.notifikasi.index') }}" class="sidebar-link @yield('sidebar-notifikasi')" id="nav-notifikasi">
            <i class="fas fa-bell"></i>
            <span>Notifikasi</span>
        </a>
        <a href="{{ route('admin.pengiriman.index') }}" class="sidebar-link @yield('sidebar-pengiriman')" id="nav-pengiriman">
            <i class="fas fa-truck"></i>
            <span>Pengiriman</span>
        </a>
    </nav>

    {{-- BOTTOM SECTION --}}
    <div class="sidebar-bottom">
        <div class="sidebar-profile" style="padding-bottom: 12px; border-top: 1px solid rgba(255,255,255,0.08);">
            <div class="sidebar-avatar">
                <span>{{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'A', 0, 1)) }}</span>
            </div>
            <div class="sidebar-profile-info">
                <span class="sidebar-profile-name">{{ Auth::user()->nama_lengkap ?? 'Administrator' }}</span>
                <span class="sidebar-profile-role">ADMINISTRATOR</span>
            </div>
        </div>
        <div style="padding: 0 16px 20px;">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn-logout" style="width: 100%; justify-content: center; margin-left: 0;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </div>
</aside>
