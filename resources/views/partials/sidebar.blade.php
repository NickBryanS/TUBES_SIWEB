{{-- Premium User Sidebar (Gambar 3) --}}
<aside class="user-sidebar" id="user-sidebar">
    <div class="sidebar-top-wrapper">
        {{-- Brand Logo --}}
        <a href="/" class="sidebar-brand">
            <i class="fas fa-mountain"></i>
            <span>Gardakala Outdoor</span>
        </a>
        
        {{-- Navigation Menu --}}
        <nav class="sidebar-menu">
            <a href="/dashboard" class="sidebar-menu-item @yield('nav-dashboard')" id="sidebar-dashboard">
                <i class="fas fa-cubes"></i>
                <span>Dashboard</span>
            </a>
            <a href="/katalog" class="sidebar-menu-item @yield('nav-katalog')" id="sidebar-katalog">
                <i class="fas fa-compass"></i>
                <span>Katalog Alat</span>
            </a>
            <a href="/riwayat" class="sidebar-menu-item @yield('nav-rental')" id="sidebar-pesanan">
                <i class="far fa-calendar-check"></i>
                <span>Pesanan Saya</span>
            </a>
        </nav>
    </div>

    {{-- Bottom Section: Help Card & Logout --}}
    <div class="sidebar-bottom-wrapper">
        {{-- Help Card --}}
        <div class="sidebar-help-card">
            <div class="help-icon-circle">
                <i class="fas fa-headset"></i>
            </div>
            <h4>Butuh Bantuan?</h4>
            <p>Tim support kami siap membantu kelancaran petualangan Anda.</p>
            <a href="https://wa.me/6287715778007" target="_blank" class="btn-help-contact">WhatsApp Support</a>
        </div>
        
        {{-- Logout Form --}}
        <form method="POST" action="{{ route('logout') }}" class="sidebar-logout-form">
            @csrf
            <button type="submit" class="btn-sidebar-logout">
                <i class="fas fa-sign-out-alt"></i>
                <span>Keluar</span>
            </button>
        </form>
    </div>
</aside>
