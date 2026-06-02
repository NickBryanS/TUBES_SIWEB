<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Executive - Gardakala Outdoor')</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/superadmin/superadmin.css') }}">
    @yield('styles')
</head>
<body>
    {{-- SIDEBAR --}}
    @include('superadmin.partials.sidebar')

    {{-- MAIN WRAPPER --}}
    <div class="admin-main">
        {{-- TOP BAR --}}
        <header class="admin-topbar" id="admin-topbar">
            <div class="topbar-left">
                <span class="topbar-section-label">Store Management</span>
                <div class="topbar-search">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Cari laporan..." id="admin-search">
                </div>
            </div>
            <div class="topbar-right">
                <div class="sa-topbar-avatar">
                    <span>{{ strtoupper(substr(Auth::user()->nama_lengkap ?? 'P', 0, 1)) }}</span>
                </div>
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
</body>
</html>
