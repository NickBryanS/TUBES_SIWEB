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
</body>
</html>
