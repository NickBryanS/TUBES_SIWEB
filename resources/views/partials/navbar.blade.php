{{-- Partials: Navbar Redesign --}}
<nav class="navbar" id="navbar">
    <div class="nav-container">
        {{-- Left: Logo --}}
        <a href="/" class="nav-logo" id="nav-logo">
            <i class="fas fa-mountain"></i>
            <span>Gardakala Outdoor</span>
        </a>

        {{-- Right: Icons --}}
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

            {{-- Profile / Login --}}
            @auth
                <a href="/dashboard" class="nav-icon-link" aria-label="Profil" title="Profil Saya">
                    <i class="far fa-user-circle"></i>
                </a>
            @else
                <a href="/login" class="btn-nav-login">Masuk</a>
            @endauth
        </div>
    </div>
</nav>

<script>
    function toggleNotificationMenu() {
        var menu = document.getElementById('notification-menu');
        if (menu) {
            menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
        }
    }
    document.addEventListener('click', function(e) {
        var notifWrapper = document.querySelector('.nav-dropdown-wrapper');
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            var menu = document.getElementById('notification-menu');
            if (menu) menu.style.display = 'none';
        }
    });
</script>

