{{-- Partials: Navbar Redesign --}}
<nav class="navbar" id="navbar">
    <div class="nav-container">
        {{-- Left: Logo --}}
        <a href="/" class="nav-logo" id="nav-logo">
            <i class="fas fa-mountain"></i>
            <span>Gardakala Outdoor</span>
        </a>

        {{-- Center: Big Search Bar --}}
        <div class="nav-search-container">
            <form action="/katalog" method="GET" class="nav-search-form">
                <div class="nav-search-wrapper">
                    <i class="fas fa-search nav-search-icon"></i>
                    <input type="text" name="search" placeholder="Cari tenda, carrier, sleeping bag..." value="{{ request('search') }}" class="nav-search-input">
                </div>
            </form>
        </div>

        {{-- Right: Icons & Profile --}}
        <div class="nav-icons" id="nav-icons">
            {{-- Wishlist Icon --}}
            <a href="/wishlist" class="nav-icon-link" aria-label="Wishlist" title="Wishlist">
                <i class="far fa-heart"></i>
                @php
                    $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
                    $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
                @endphp
                @if($wishlistCount > 0)
                    <span class="nav-badge badge-red">{{ $wishlistCount }}</span>
                @endif
            </a>

            {{-- Notification Icon --}}
            @auth
                <div class="nav-dropdown-wrapper">
                    <button class="nav-icon-link" aria-label="Notifikasi" id="notification-toggle" onclick="toggleNotificationMenu()">
                        <i class="far fa-bell"></i>
                        @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="nav-badge badge-red">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown-menu" id="notification-menu">
                        <div class="dropdown-header">
                            <span>Notifikasi</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifikasi.read') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" class="btn-mark-read">Tandai Dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div class="dropdown-body">
                            @forelse(Auth::user()->notifications()->limit(5)->get() as $notification)
                                <div class="dropdown-item {{ $notification->read_at ? '' : 'unread' }}">
                                    <p>{{ $notification->data['message'] ?? 'Ada pembaruan pesanan.' }}</p>
                                    <small>{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <div class="dropdown-empty">Tidak ada notifikasi</div>
                            @endforelse
                        </div>
                        <div class="dropdown-footer">
                            <a href="{{ route('riwayat') }}">Lihat Semua Pesanan</a>
                        </div>
                    </div>
                </div>
            @else
                <a href="/login" class="nav-icon-link" aria-label="Notifikasi" title="Notifikasi">
                    <i class="far fa-bell"></i>
                </a>
            @endauth

            {{-- Cart Icon --}}
            <a href="/keranjang" class="nav-icon-link" aria-label="Keranjang" title="Keranjang">
                <i class="fas fa-shopping-cart"></i>
                @php
                    $cartCount = \App\Models\Cart::where('user_id', $userId)->sum('quantity');
                @endphp
                @if($cartCount > 0)
                    <span class="nav-badge badge-red">{{ $cartCount }}</span>
                @endif
            </a>

            {{-- Profile --}}
            @auth
                <div class="nav-profile-dropdown-wrapper">
                    <button class="nav-profile-trigger" id="profile-toggle" onclick="toggleProfileMenu()">
                        <img src="{{ Auth::user()->url_avatar ?? asset('images/avatar-default.png') }}" alt="{{ Auth::user()->name }}" class="nav-avatar-img">
                        <span class="nav-profile-name">Hai, {{ explode(' ', Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Petualang')[0] }}</span>
                        <i class="fas fa-chevron-down nav-arrow-icon"></i>
                    </button>
                    <div class="profile-dropdown-menu" id="profile-menu">
                        <div class="profile-dropdown-header">
                            <h4>{{ Auth::user()->nama_lengkap ?? Auth::user()->name }}</h4>
                            <p>{{ Auth::user()->email }}</p>
                        </div>
                        <div class="profile-dropdown-body">
                            <a href="/dashboard" class="profile-dropdown-item">
                                <i class="far fa-user"></i> Dashboard Saya
                            </a>
                            <a href="/riwayat" class="profile-dropdown-item">
                                <i class="far fa-file-alt"></i> Pesanan Saya
                            </a>
                            <a href="/wishlist" class="profile-dropdown-item">
                                <i class="far fa-heart"></i> Wishlist
                            </a>
                        </div>
                        <form method="POST" action="{{ route('logout') }}" class="profile-logout-form">
                            @csrf
                            <button type="submit" class="btn-profile-logout">
                                <i class="far fa-sign-out"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <a href="/login" class="btn-nav-login">Masuk</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleProfileMenu() {
        var menu = document.getElementById('profile-menu');
        var notifMenu = document.getElementById('notification-menu');
        if (menu) {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        if (notifMenu) {
            notifMenu.style.display = 'none';
        }
    }
    function toggleNotificationMenu() {
        var menu = document.getElementById('notification-menu');
        var profileMenu = document.getElementById('profile-menu');
        if (menu) {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
        if (profileMenu) {
            profileMenu.style.display = 'none';
        }
    }
    document.addEventListener('click', function(e) {
        var profileWrapper = document.querySelector('.nav-profile-dropdown-wrapper');
        var notifWrapper = document.querySelector('.nav-dropdown-wrapper');
        
        if (profileWrapper && !profileWrapper.contains(e.target)) {
            var menu = document.getElementById('profile-menu');
            if (menu) menu.style.display = 'none';
        }
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            var menu = document.getElementById('notification-menu');
            if (menu) menu.style.display = 'none';
        }
    });
</script>
