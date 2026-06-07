<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin - Gardakala Outdoor')</title>
    <meta name="description" content="@yield('description', 'Admin Portal - Gardakala Outdoor')">
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    @yield('styles')
</head>
<body>
    {{-- SIDEBAR --}}
    @include('admin.partials.sidebar')
    <div class="admin-sidebar-overlay" id="admin-sidebar-overlay"></div>

    {{-- MAIN WRAPPER --}}
    <div class="admin-main">
    {{-- TOP BAR --}}
    <header class="admin-topbar" id="admin-topbar">
        <div class="topbar-left">
            <button class="admin-sidebar-toggle" id="admin-sidebar-toggle" aria-label="Toggle Sidebar">
                <i class="fas fa-bars"></i>
            </button>
            <span class="topbar-section-label">Store Management</span>

        </div>
        <div class="topbar-right">
            @php
                // Hitung notifikasi aktif secara dinamis
                $notifCount = \App\Models\Transaction::whereIn('status_transaksi', ['menunggu', 'menunggu_admin'])->count()
                            + \App\Models\Payment::where('status_pembayaran', 'menunggu')->count();
            @endphp
            <a href="{{ route('admin.notifikasi.index') }}" class="topbar-icon" id="btn-notification"
               aria-label="Notifikasi" title="Pusat Notifikasi" style="text-decoration:none; position:relative;">
                <i class="fas fa-bell"></i>
                @if($notifCount > 0)
                    <span class="topbar-badge">{{ $notifCount > 99 ? '99+' : $notifCount }}</span>
                @endif
            </a>
            <a href="{{ route('admin.pengguna.export') }}" class="btn-ekspor" id="btn-ekspor">
                <i class="fas fa-download"></i> Ekspor Data
            </a>
        </div>
    </header>

        {{-- PAGE CONTENT --}}
        <div class="admin-content">
            @if(session('success'))
            <div class="admin-alert admin-alert-success" id="admin-alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
            </div>
            @endif

            @if(session('error'))
            <div class="admin-alert admin-alert-error" id="admin-alert">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="alert-close">&times;</button>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    @yield('scripts')

    {{-- Admin Sidebar Toggle Script --}}
    <script>
    (function() {
        var toggleBtn = document.getElementById('admin-sidebar-toggle');
        var sidebar = document.getElementById('admin-sidebar');
        var overlay = document.getElementById('admin-sidebar-overlay');

        function openSidebar() {
            if (sidebar) sidebar.classList.add('open');
            if (overlay) overlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeSidebar() {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            document.body.style.overflow = '';
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function() {
                if (sidebar && sidebar.classList.contains('open')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }
        if (overlay) {
            overlay.addEventListener('click', closeSidebar);
        }
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) closeSidebar();
        });
    })();
    </script>
</body>
</html>
