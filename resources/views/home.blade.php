@extends('layouts.app')

@section('title', 'Gardakala Outdoor - Sewa Alat Outdoor')
@section('description', 'Gardakala Outdoor - Sewa alat outdoor terlengkap. Alam menunggu, kami siapkan semua.')
@section('nav-home', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
    {{-- HERO SECTION (partial) --}}
    @include('partials.hero')

    {{-- STATS SECTION --}}
    <section class="stats-section" id="stats-section">
        <div class="stats-container">
            <div class="stat-item">
                <span class="stat-number">100+</span>
                <span class="stat-label">ALAT OUTDOOR</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-number">50+</span>
                <span class="stat-label">BRAND TERPUAYA</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-number">4.9+</span>
                <span class="stat-label">RATING KEPUASAN</span>
            </div>
        </div>
    </section>

    {{-- POPULAR EQUIPMENT SECTION --}}
    <section class="popular-section" id="popular-section">
        <div class="section-container">
            <div class="section-header">
                <div>
                    <h2 class="section-title">Perlengkapan Terpopuler</h2>
                    <p class="section-subtitle">Gear pilihan terbaik untuk ekspedisi Anda bulan ini.</p>
                </div>
                <a href="/katalog" class="see-all-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="products-grid">
                {{-- Product 1 --}}
                <div class="product-card" id="product-1">
                    <div class="product-image-wrapper">
                        <span class="product-badge badge-recommended">PALING DICARI</span>
                        <img src="{{ asset('images/tent-product.png') }}" alt="Tendaki Borneo Orange 4P" class="product-image">
                    </div>
                    <div class="product-info">
                        <div class="product-name-row">
                            <h3 class="product-name">Tendaki Borneo Orange 4P</h3>
                            <div class="product-rating"><i class="fas fa-star"></i> 4.9</div>
                        </div>
                        <p class="product-brand">The North Face • Ultra Light</p>
                        <div class="product-price-row">
                            <span class="product-price">Rp 125.000<span class="price-period">/hari</span></span>
                            <div class="product-actions">
                                <button class="action-btn" aria-label="Favorite"><i class="far fa-heart"></i></button>
                                <button class="action-btn" aria-label="Share"><i class="fas fa-share-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Product 2 --}}
                <div class="product-card" id="product-2">
                    <div class="product-image-wrapper">
                        <span class="product-badge badge-new">BARU</span>
                        <img src="{{ asset('images/carrier-product.png') }}" alt="Summit Carrier 60L" class="product-image">
                    </div>
                    <div class="product-info">
                        <div class="product-name-row">
                            <h3 class="product-name">Summit Carrier 60L</h3>
                            <div class="product-rating"><i class="fas fa-star"></i> 4.8</div>
                        </div>
                        <p class="product-brand">Osprey • Anti Gravity System</p>
                        <div class="product-price-row">
                            <span class="product-price">Rp 85.000<span class="price-period">/hari</span></span>
                            <div class="product-actions">
                                <button class="action-btn" aria-label="Favorite"><i class="far fa-heart"></i></button>
                                <button class="action-btn" aria-label="Share"><i class="fas fa-share-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Product 3 --}}
                <div class="product-card" id="product-3">
                    <div class="product-image-wrapper">
                        <span class="product-badge badge-top">TOP CHOICE</span>
                        <img src="{{ asset('images/sleeping-bag-product.png') }}" alt="Arctic Dreamer Bag" class="product-image">
                    </div>
                    <div class="product-info">
                        <div class="product-name-row">
                            <h3 class="product-name">Arctic Dreamer Bag</h3>
                            <div class="product-rating"><i class="fas fa-star"></i> 4.7</div>
                        </div>
                        <p class="product-brand">Deuter • Dreamlite Series</p>
                        <div class="product-price-row">
                            <span class="product-price">Rp 60.000<span class="price-period">/hari</span></span>
                            <div class="product-actions">
                                <button class="action-btn" aria-label="Favorite"><i class="far fa-heart"></i></button>
                                <button class="action-btn" aria-label="Share"><i class="fas fa-share-alt"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS SECTION --}}
    <section class="how-it-works" id="how-it-works">
        <div class="section-container">
            <h2 class="section-title center">Cara Kerja Kami</h2>
            <div class="steps-grid">
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-search"></i>
                    </div>
                    <h3 class="step-title">Pilih Alat</h3>
                    <p class="step-desc">Telusuri katalog lengkap kami dan pilih gear yang sesuai dengan kebutuhan petualangan Anda.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="step-title">Tentukan Tanggal</h3>
                    <p class="step-desc">Pilih durasi sewa mulai dari harian, mingguan, kami yang siap siap dan mudah digunakan.</p>
                </div>
                <div class="step-card">
                    <div class="step-icon-wrapper">
                        <i class="fas fa-mountain"></i>
                    </div>
                    <h3 class="step-title">Ambil & Jelajahi</h3>
                    <p class="step-desc">Ambil di lokasi atau kirim ke alamat Anda. Kit siap, saatnya bertualang! petualangan Anda.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- PRIORITY / WHY US SECTION --}}
    <section class="priority-section" id="priority-section">
        <div class="section-container priority-container">
            <div class="priority-left">
                <div class="priority-features-grid">
                    <div class="feature-card">
                        <div class="feature-icon green"><i class="fas fa-tools"></i></div>
                        <h4 class="feature-title">Alat Terawat</h4>
                        <p class="feature-desc">Semua alat dalam pengecekan dan perawatan rutin semua.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon orange"><i class="fas fa-tags"></i></div>
                        <h4 class="feature-title">Harga Bersahabat</h4>
                        <p class="feature-desc">Tarif kompetitif untuk semua jenis sewa alat outdoor.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon blue"><i class="fas fa-truck"></i></div>
                        <h4 class="feature-title">Pengiriman</h4>
                        <p class="feature-desc">Layanan antar jemput yang cepat & aman.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon red"><i class="fas fa-calendar-check"></i></div>
                        <h4 class="feature-title">Booking Mudah</h4>
                        <p class="feature-desc">Pesan alat outdoor hanya dalam beberapa klik.</p>
                    </div>
                </div>
            </div>
            <div class="priority-right">
                <h2 class="priority-title">Keamanan dan Kenyamanan Anda Adalah Prioritas Kami</h2>
                <p class="priority-desc">Di Gardakala Outdoor, kami memahami bahwa setiap petualangan di alam liar memerlukan kepercayaan. Itu mengapa kami hanya menyediakan alat dan gear dari brand-brand terkemuka yang telah teruji oleh para petualang berpengalaman.</p>
                <ul class="priority-checklist">
                    <li><i class="fas fa-check-circle"></i> Pengecekan Double Check setiap kali meminjamkan</li>
                    <li><i class="fas fa-check-circle"></i> Garansi Penggantian jika terdapat kerusakan</li>
                    <li><i class="fas fa-check-circle"></i> Sedia alat Terbaik, berkualitas tinggi serta berstandar</li>
                </ul>
            </div>
        </div>
    </section>
@endsection
