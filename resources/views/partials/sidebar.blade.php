{{-- User Sidebar Minimalis --}}
<aside class="user-sidebar" id="user-sidebar">
    <nav class="sidebar-menu">
        <a href="/" class="sidebar-menu-item @yield('nav-home')" id="sidebar-home">
            <i class="far fa-compass"></i>
            <span>Beranda</span>
        </a>
        <a href="/katalog" class="sidebar-menu-item @yield('nav-katalog')" id="sidebar-katalog">
            <i class="far fa-folder-open"></i>
            <span>Katalog</span>
        </a>
        <a href="/riwayat" class="sidebar-menu-item @yield('nav-rental')" id="sidebar-pesanan">
            <i class="far fa-file-alt"></i>
            <span>Pesanan Saya</span>
        </a>

        <a href="/dashboard" class="sidebar-menu-item @yield('nav-dashboard')" id="sidebar-dashboard">
            <i class="far fa-user"></i>
            <span>Profil</span>
        </a>
    </nav>

    {{-- Help Card at bottom --}}
    <div class="sidebar-help-card">
        <div class="help-icon-circle">
            <i class="fas fa-headset"></i>
        </div>
        <h4>Butuh bantuan?</h4>
        <p>Kami siap membantu kelancaran petualangan Anda.</p>
        <a href="https://wa.me/6287715778007" target="_blank" class="btn-help-contact">Hubungi Kami</a>
    </div>
</aside>
