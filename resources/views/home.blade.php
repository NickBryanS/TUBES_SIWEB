@extends('layouts.app')

@section('title', 'Gardakala Outdoor - Sewa Alat Outdoor')
@section('description', 'Gardakala Outdoor - Sewa perlengkapan outdoor premium tanpa harus membeli mahal.')
@section('nav-home', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<div class="home-page-content">
    {{-- HERO SECTION --}}
    @include('partials.hero')

    {{-- KATEGORI POPULER --}}
    <section class="kategori-populer-section">
        <div class="home-section-header">
            <h2 class="home-section-title">Kategori Populer</h2>
            <a href="/katalog" class="home-see-all">Lihat Semua</a>
        </div>
        <div class="kategori-grid">
            {{-- Camping Card --}}
            <a href="/katalog?category=tenda" class="kategori-card">
                <div class="kategori-icon-wrapper">
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="Camping - Gardakala Outdoor" class="kategori-img">
                </div>
                <div class="kategori-text">
                    <h3>Camping</h3>
                    <p>12+ Peralatan</p>
                </div>
            </a>

            {{-- Hiking Card --}}
            <a href="/katalog?category=tas-carrier" class="kategori-card">
                <div class="kategori-icon-wrapper">
                    <img src="{{ asset('images/backpack-product.png') }}" alt="Hiking - Gardakala Outdoor" class="kategori-img">
                </div>
                <div class="kategori-text">
                    <h3>Hiking</h3>
                    <p>10+ Peralatan</p>
                </div>
            </a>

            {{-- Cooking Card --}}
            <a href="/katalog?category=alat-masak" class="kategori-card">
                <div class="kategori-icon-wrapper">
                    <img src="{{ asset('images/stove-product.png') }}" alt="Cooking - Gardakala Outdoor" class="kategori-img">
                </div>
                <div class="kategori-text">
                    <h3>Cooking</h3>
                    <p>8+ Peralatan</p>
                </div>
            </a>

            {{-- Sleeping Gear Card --}}
            <a href="/katalog?category=alat-tidur" class="kategori-card">
                <div class="kategori-icon-wrapper">
                    <img src="{{ asset('images/sleepingbag-product.png') }}" alt="Sleeping Gear - Gardakala Outdoor" class="kategori-img">
                </div>
                <div class="kategori-text">
                    <h3>Sleeping Gear</h3>
                    <p>7+ Peralatan</p>
                </div>
            </a>
        </div>
    </section>

    {{-- REKOMENDASI UNTUKMU --}}
    <section class="rekomendasi-section">
        <div class="home-section-header">
            <h2 class="home-section-title">Rekomendasi Untukmu</h2>
            <a href="/katalog" class="home-see-all">Lihat Semua</a>
        </div>
        
        @php
            $products = \App\Models\Product::with('category')->take(4)->get();
        @endphp

        <div class="rekomendasi-grid">
            @forelse($products as $product)
                @php
                    $productId    = $product->id;
                    $productImage = $product->url_gambar ?? 'images/tent-expedition.png';
                    $productName  = $product->nama_produk;
                    $productPrice = 'Rp ' . number_format($product->harga_sewa, 0, ',', '.');
                    $categoryName = $product->category->nama_kategori ?? 'Outdoor';
                @endphp
                <div class="rekomendasi-card-wrapper">
                    <a href="{{ route('produk.detail', $productId) }}" class="rekomendasi-card">
                        <div class="rekomendasi-image-box">
                            <span class="rekomendasi-badge">{{ $categoryName }}</span>
                            <img src="{{ asset($productImage) }}" alt="{{ $productName }}" class="rekomendasi-img">
                        </div>
                        <div class="rekomendasi-info">
                            <div class="rekomendasi-meta">
                                <span class="rekomendasi-rating">
                                    <i class="fas fa-star"></i> {{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '4.8' }}
                                </span>
                            </div>
                            <h3 class="rekomendasi-name">{{ $productName }}</h3>
                            <div class="rekomendasi-price-box">
                                <span class="rekomendasi-price">{{ $productPrice }}<span class="price-unit">/hari</span></span>
                            </div>
                        </div>
                    </a>
                </div>
            @empty
                <div class="empty-products">Belum ada perlengkapan outdoor tersedia saat ini.</div>
            @endforelse
        </div>
    </section>

    {{-- HOW IT WORKS SECTION (Simple step info) --}}
    <section class="how-it-works-section" id="how-it-works">
        <div class="home-section-header center-header">
            <h2 class="home-section-title">Cara Rental Mudah</h2>
            <p class="home-section-subtitle">Mulai petualangan luar ruangan Anda dengan 3 langkah sederhana.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon">
                    <i class="far fa-compass"></i>
                </div>
                <h3>Pilih Perlengkapan</h3>
                <p>Cari alat camping atau mendaki premium dari katalog lengkap kami.</p>
            </div>
            <div class="step-card">
                <div class="step-icon">
                    <i class="far fa-calendar-alt"></i>
                </div>
                <h3>Tentukan Jadwal sewa</h3>
                <p>Pilih durasi tanggal sewa yang sesuai dengan rencana petualangan Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-icon">
                    <i class="far fa-check-circle"></i>
                </div>
                <h3>Ambil & Mulai Beraksi</h3>
                <p>Ambil pesanan di toko atau minta pengiriman langsung ke alamat Anda.</p>
            </div>
        </div>
    </section>
</div>
@endsection
