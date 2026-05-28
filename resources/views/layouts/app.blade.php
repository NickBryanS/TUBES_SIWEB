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
    {{-- NAVBAR (partial) --}}
    @include('partials.navbar')

    {{-- MAIN APP CONTAINER --}}
    <div class="app-layout-container">
        {{-- SIDEBAR (partial) --}}
        @if(!Route::is('wishlist.index'))
            @include('partials.sidebar')
        @endif

        {{-- MAIN CONTENT AREA --}}
        <div class="app-content-wrapper">
            <main class="main-content">
                @yield('content')
            </main>

            {{-- FOOTER (partial) --}}
            @include('partials.footer')
        </div>
    </div>

    @yield('scripts')
</body>
</html>
