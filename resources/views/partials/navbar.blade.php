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
            <a href="/keranjang" class="nav-icon" aria-label="Cart" style="position: relative;">
                <i class="fas fa-shopping-cart"></i>
                @php
                    $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
                    $cartCount = \App\Models\Cart::where('user_id', $userId)->sum('quantity');
                @endphp
                @if($cartCount > 0)
                    <span style="position: absolute; top: -5px; right: -10px; background-color: #e63946; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; line-height: 1;">{{ $cartCount }}</span>
                @endif
            </a>
            <a href="#" class="nav-icon" aria-label="User"><i class="fas fa-user"></i></a>
        </div>
    </div>
</nav>
