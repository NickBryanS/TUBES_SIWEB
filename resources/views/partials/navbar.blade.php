{{-- Partials: Navbar --}}
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="/" class="nav-logo" id="nav-logo">Gardakala Outdoor</a>
        <ul class="nav-links" id="nav-links">
            <li><a href="/" class="nav-link @yield('nav-home')">Home</a></li>
            <li><a href="/dashboard" class="nav-link @yield('nav-dashboard')">Dashboard</a></li>
            <li><a href="/katalog" class="nav-link @yield('nav-katalog')">Katalog</a></li>
            <li><a href="/riwayat" class="nav-link @yield('nav-rental')">Rental</a></li>
        </ul>
        <div class="nav-icons" id="nav-icons">
            <a href="/keranjang" class="nav-icon" aria-label="Cart"><i class="fas fa-shopping-cart"></i></a>
            <a href="#" class="nav-icon" aria-label="User"><i class="fas fa-user"></i></a>
        </div>
    </div>
</nav>
