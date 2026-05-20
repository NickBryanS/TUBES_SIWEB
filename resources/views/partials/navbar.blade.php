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
            <a href="/wishlist" class="nav-icon" aria-label="Wishlist" style="position: relative; margin-right: 15px;">
                <i class="far fa-heart"></i>
                @php
                    $userId = \Illuminate\Support\Facades\Auth::id() ?? 1;
                    $wishlistCount = \App\Models\Wishlist::where('user_id', $userId)->count();
                @endphp
                @if($wishlistCount > 0)
                    <span style="position: absolute; top: -5px; right: -10px; background-color: #e63946; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; line-height: 1;">{{ $wishlistCount }}</span>
                @endif
            </a>
            <a href="/keranjang" class="nav-icon" aria-label="Cart" style="position: relative;">
                <i class="fas fa-shopping-cart"></i>
                @php
                    $cartCount = \App\Models\Cart::where('user_id', $userId)->sum('quantity');
                @endphp
                @if($cartCount > 0)
                    <span style="position: absolute; top: -5px; right: -10px; background-color: #e63946; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; line-height: 1;">{{ $cartCount }}</span>
                @endif
            </a>
            @auth
                <!-- Notification Bell -->
                <div class="nav-notification-dropdown" style="position: relative; margin-right: 15px; margin-left: 15px;">
                    <button class="nav-icon" aria-label="Notifications" id="notification-toggle" onclick="toggleNotificationMenu()" style="background:none;border:none;cursor:pointer;color:inherit;font-size:inherit;padding:0;position:relative;">
                        <i class="fas fa-bell"></i>
                        @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
                        @if($unreadCount > 0)
                            <span style="position: absolute; top: -5px; right: -10px; background-color: #e63946; color: white; border-radius: 50%; padding: 2px 6px; font-size: 11px; font-weight: bold; line-height: 1;">{{ $unreadCount }}</span>
                        @endif
                    </button>
                    <div class="notification-dropdown-menu" id="notification-menu" style="display:none;position:absolute;right:0;top:calc(100% + 10px);background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.12);min-width:280px;z-index:100;overflow:hidden;">
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;display:flex;justify-content:space-between;align-items:center;">
                            <span style="font-weight:600;font-size:0.85rem;color:#1a1a1a;">Notifikasi</span>
                            @if($unreadCount > 0)
                                <form action="{{ route('notifikasi.read') }}" method="POST" style="margin:0;">
                                    @csrf
                                    <button type="submit" style="background:none;border:none;color:#2563eb;font-size:0.75rem;cursor:pointer;padding:0;">Tandai Dibaca</button>
                                </form>
                            @endif
                        </div>
                        <div style="max-height:300px;overflow-y:auto;padding:0;">
                            @forelse(Auth::user()->notifications()->limit(5)->get() as $notification)
                                <div style="padding:12px 16px;border-bottom:1px solid #f9fafb;background:{{ $notification->read_at ? '#fff' : '#f0f9ff' }};">
                                    <p style="margin:0;font-size:0.8rem;color:#374151;">{{ $notification->data['message'] ?? 'Ada pembaruan pesanan.' }}</p>
                                    <small style="color:#9ca3af;font-size:0.7rem;">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            @empty
                                <div style="padding:16px;text-align:center;font-size:0.8rem;color:#9ca3af;">Tidak ada notifikasi</div>
                            @endforelse
                        </div>
                        <div style="padding:10px;text-align:center;border-top:1px solid #f0f0f0;background:#f9fafb;">
                            <a href="{{ route('riwayat') }}" style="font-size:0.75rem;color:#2563eb;text-decoration:none;font-weight:500;">Lihat Pesanan</a>
                        </div>
                    </div>
                </div>
                <div class="nav-profile-dropdown" style="position: relative;">
                    <button class="nav-icon" aria-label="User" id="profile-toggle" onclick="toggleProfileMenu()" style="background:none;border:none;cursor:pointer;color:inherit;font-size:inherit;padding:0;">
                        <i class="fas fa-user"></i>
                    </button>
                    <div class="profile-dropdown-menu" id="profile-menu" style="display:none;position:absolute;right:0;top:calc(100% + 10px);background:#fff;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.12);min-width:180px;z-index:100;overflow:hidden;">
                        <div style="padding:14px 16px;border-bottom:1px solid #f0f0f0;">
                            <div style="font-weight:600;font-size:0.85rem;color:#1a1a1a;">{{ Auth::user()->nama_lengkap ?? Auth::user()->email }}</div>
                            <div style="font-size:0.75rem;color:#9ca3af;margin-top:2px;">{{ Auth::user()->email }}</div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="width:100%;padding:12px 16px;background:none;border:none;cursor:pointer;display:flex;align-items:center;gap:8px;font-size:0.84rem;color:#dc2626;font-family:inherit;transition:background 0.2s;">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </button>
                        </form>
                    </div>
                </div>
                <script>
                    function toggleProfileMenu() {
                        var menu = document.getElementById('profile-menu');
                        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                        document.getElementById('notification-menu').style.display = 'none';
                    }
                    function toggleNotificationMenu() {
                        var menu = document.getElementById('notification-menu');
                        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
                        document.getElementById('profile-menu').style.display = 'none';
                    }
                    document.addEventListener('click', function(e) {
                        var profileDropdown = document.querySelector('.nav-profile-dropdown');
                        var notifDropdown = document.querySelector('.nav-notification-dropdown');
                        
                        if (profileDropdown && !profileDropdown.contains(e.target) && notifDropdown && !notifDropdown.contains(e.target)) {
                            document.getElementById('profile-menu').style.display = 'none';
                            document.getElementById('notification-menu').style.display = 'none';
                        }
                    });
                </script>
            @else
                <a href="/login" class="nav-icon" aria-label="Login"><i class="fas fa-user"></i></a>
            @endauth
        </div>
    </div>
</nav>
