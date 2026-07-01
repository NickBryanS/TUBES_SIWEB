<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Gardakala Outdoor')</title>
    <meta name="description" content="@yield('description', 'Gardakala Outdoor - Sewa alat outdoor terlengkap.')">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('styles')
</head>
<body>
    @php
        $isPortal = Auth::check() && !Request::is('/');
    @endphp

    @if(!$isPortal)
        {{-- NAVBAR (partial) --}}
        @include('partials.navbar')
    @endif

    {{-- MAIN APP CONTAINER --}}
    <div class="app-layout-container {{ $isPortal ? 'portal-layout' : 'public-layout' }}">
        {{-- SIDEBAR (partial) --}}
        @if($isPortal)
            @include('partials.sidebar')
            <div class="sidebar-overlay" id="sidebar-overlay"></div>
        @endif

        {{-- MAIN CONTENT AREA --}}
        <div class="app-content-wrapper">
            @if($isPortal)
                {{-- PORTAL HEADER (partial) --}}
                @include('partials.portal-header')
            @endif

            <main class="main-content">
                @yield('content')
            </main>

            {{-- FOOTER (partial) --}}
            @if(!$isPortal)
                @include('partials.footer')
            @endif
        </div>
    </div>

    @yield('scripts')

    {{-- Sidebar Toggle Script (portal pages) --}}
    @if($isPortal)
    <script>
    (function() {
        var toggleBtn = document.getElementById('sidebar-toggle');
        var sidebar = document.getElementById('user-sidebar');
        var overlay = document.getElementById('sidebar-overlay');

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

        // Close sidebar on resize to desktop
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                closeSidebar();
            }
        });
    })();
    </script>
    @endif
    
    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/6287715778007?text=Halo%20Gardakala%20Outdoor%2C%20saya%20ingin%20tanya%20tentang%20penyewaan%20alat." 
       target="_blank" 
       class="wa-float" 
       style="position: fixed; width: 60px; height: 60px; bottom: 40px; right: 40px; background-color: #25d366; color: #FFF; border-radius: 50px; text-align: center; font-size: 30px; box-shadow: 2px 2px 10px rgba(0,0,0,0.2); z-index: 100; display: flex; align-items: center; justify-content: center; text-decoration: none; transition: transform 0.3s ease, background-color 0.3s ease;">
        <i class="fab fa-whatsapp my-float"></i>
    </a>
    
    <style>
        .wa-float:hover {
            transform: scale(1.1);
            background-color: #128c7e;
        }
    </style>
</body>
</html>
