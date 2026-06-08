{{-- Partials: Portal Header --}}
<header class="portal-header">
    <div class="portal-header-left">
        {{-- Burger button for mobile navigation --}}
        <button class="btn-sidebar-toggle" id="sidebar-toggle" aria-label="Toggle Sidebar">
            <i class="fas fa-bars"></i>
        </button>
        
        {{-- Elegant welcome text / search --}}
    </div>
    
    <div class="portal-header-right">
        @php
            $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
            $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
            $cartCount = \App\Models\Cart::where('user_id', $userId)->sum('quantity');
        @endphp

        {{-- Wishlist Icon --}}
        <a href="/wishlist" class="portal-icon-link" aria-label="Wishlist" title="Wishlist">
            <i class="far fa-heart"></i>
            @if($wishlistCount > 0)
                <span class="portal-badge badge-green">{{ $wishlistCount }}</span>
            @endif
        </a>

        {{-- Notification Bell with Dropdown --}}
        @auth
            <div class="portal-dropdown-wrapper">
                <button class="portal-icon-link" aria-label="Notifikasi" id="portal-notification-toggle" onclick="togglePortalNotificationMenu()">
                    <i class="far fa-bell"></i>
                    @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                    @if($unreadCount > 0)
                        <span class="portal-badge badge-green">{{ $unreadCount }}</span>
                    @endif
                </button>
                <div class="portal-notification-dropdown" id="portal-notification-menu">
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
        @endauth

        {{-- Cart Icon --}}
        <a href="/keranjang" class="portal-icon-link" aria-label="Keranjang" title="Keranjang Saya">
            <i class="fas fa-shopping-cart"></i>
            @if($cartCount > 0)
                <span class="portal-badge badge-green">{{ $cartCount }}</span>
            @endif
        </a>

        {{-- User Profile Name Only --}}
        <a href="/dashboard" class="portal-profile-trigger">
            <i class="fas fa-user-circle" style="font-size: 1.1rem;"></i>
            <span class="portal-profile-name">{{ explode(' ', Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Petualang')[0] }}</span>
        </a>
    </div>
</header>

<script>
    function togglePortalNotificationMenu() {
        var menu = document.getElementById('portal-notification-menu');
        if (menu) {
            menu.classList.toggle('show');
        }
    }
    document.addEventListener('click', function(e) {
        var notifWrapper = document.querySelector('.portal-dropdown-wrapper');
        if (notifWrapper && !notifWrapper.contains(e.target)) {
            var menu = document.getElementById('portal-notification-menu');
            if (menu) menu.classList.remove('show');
        }
    });
</script>
