@extends('layouts.app')

@section('title', 'Gardakala Outdoor - Sewa Alat Outdoor Premium')
@section('description', 'Gardakala Outdoor - Sewa perlengkapan outdoor premium untuk pendakian, kemping, dan petualangan alam bebas.')
@section('nav-home', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endsection

@section('content')
<div class="home-page-content">
    {{-- PREMIUM HERO SECTION --}}
    @include('partials.hero')

    {{-- PERLENGKAPAN TERPOPULER (Top 3 Premium Products) --}}
    <section class="popular-equipment-section">
        <div class="home-section-header">
            <div>
                <h2 class="home-section-title">Perlengkapan Terpopuler</h2>
                <p class="home-section-subtitle">Alat-alat outdoor paling favorit pilihan para petualang sejati.</p>
            </div>
            <a href="/katalog" class="home-see-all">Lihat Semua Alat <i class="fas fa-chevron-right"></i></a>
        </div>
        
        @php
            $popularProducts = \App\Models\Product::with('category')->take(3)->get();
        @endphp

        <div class="popular-equipment-grid">
            @forelse($popularProducts as $product)
                @php
                    $productId    = $product->id;
                    $productImage = $product->url_gambar ?? 'images/tent-expedition.png';
                    $productName  = $product->nama_produk;
                    $productDesc  = $product->deskripsi;
                    $productPrice = 'Rp ' . number_format($product->harga_sewa, 0, ',', '.');
                    $categoryName = $product->category->nama_kategori ?? 'Outdoor';
                @endphp
                <div class="equipment-premium-card">
                    <div class="equipment-card-image">
                        <span class="equipment-badge">{{ $categoryName }}</span>
                        <img src="{{ asset($productImage) }}" alt="{{ $productName }}">
                    </div>
                    <div class="equipment-card-body">
                        <div class="equipment-card-meta">
                            <span class="equipment-rating">
                                <i class="fas fa-star"></i> {{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '4.8' }}
                            </span>
                            <span class="equipment-reviews">({{ $product->reviewCount() > 0 ? $product->reviewCount() : '12' }} Ulasan)</span>
                        </div>
                        <h3 class="equipment-title">{{ $productName }}</h3>
                        <p class="equipment-desc">{{ Str::limit($productDesc, 80) }}</p>
                        <div class="equipment-card-footer">
                            <span class="equipment-price">{{ $productPrice }}<span class="price-unit">/hari</span></span>
                            <div class="equipment-actions">
                                <a href="{{ route('produk.detail', $productId) }}" class="btn-equipment-detail">Detail</a>
                                <form action="{{ route('cart.store', $productId) }}" method="POST" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <input type="hidden" name="days" value="1">
                                    <button type="submit" class="btn-equipment-cart"><i class="fas fa-shopping-bag"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-products">Belum ada perlengkapan outdoor populer yang tersedia.</div>
            @endforelse
        </div>
    </section>

    {{-- CARA KERJA KAMI (3 Steps) --}}
    <section class="how-it-works-section" id="how-it-works">
        <div class="home-section-header center-header">
            <h2 class="home-section-title">Cara Kerja Kami</h2>
            <p class="home-section-subtitle">Sewa peralatan camping & mendaki dengan 3 langkah praktis dan mudah.</p>
        </div>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-icon">
                    <i class="far fa-compass"></i>
                </div>
                <h3>Pilih Alat</h3>
                <p>Telusuri berbagai perlengkapan kemping & mendaki kelas atas dari katalog lengkap kami.</p>
            </div>
            <div class="step-card">
                <div class="step-icon">
                    <i class="far fa-calendar-check"></i>
                </div>
                <h3>Tentukan Tanggal</h3>
                <p>Atur tanggal pengambilan dan pengembalian barang sesuai dengan jadwal perjalanan Anda.</p>
            </div>
            <div class="step-card">
                <div class="step-icon">
                    <i class="fas fa-route"></i>
                </div>
                <h3>Ambil & Jelajahi</h3>
                <p>Ambil langsung barang di toko terdekat kami atau nikmati layanan pengiriman instan.</p>
            </div>
        </div>
    </section>

    {{-- KEAMANAN DAN KENYAMANAN SEBAGAI PRIORITAS --}}
    <section class="security-priority-section">
        <div class="security-grid">
            {{-- Left Side: 4 Benefit Cards Grid --}}
            <div class="security-benefits-block">
                <div class="benefit-modern-card">
                    <div class="benefit-card-icon"><i class="fas fa-circle-check"></i></div>
                    <div class="benefit-card-text">
                        <h4>Alat Terawat & Bersih</h4>
                        <p>Kami menjamin semua alat selalu steril dan dalam kondisi siap tempur di alam bebas.</p>
                    </div>
                </div>
                <div class="benefit-modern-card">
                    <div class="benefit-card-icon"><i class="fas fa-tags"></i></div>
                    <div class="benefit-card-text">
                        <h4>Harga Bersahabat</h4>
                        <p>Tarif rental yang transparan, tanpa biaya tersembunyi, ramah untuk kantong petualang.</p>
                    </div>
                </div>
                <div class="benefit-modern-card">
                    <div class="benefit-card-icon"><i class="fas fa-truck-fast"></i></div>
                    <div class="benefit-card-text">
                        <h4>Layanan Pengiriman Cepat</h4>
                        <p>Nikmati kemudahan pengiriman peralatan langsung ke basecamp atau depan rumah Anda.</p>
                    </div>
                </div>
                <div class="benefit-modern-card">
                    <div class="benefit-card-icon"><i class="fas fa-mobile-screen"></i></div>
                    <div class="benefit-card-text">
                        <h4>Booking Mudah & Cepat</h4>
                        <p>Sistem booking cerdas secara real-time kapan pun dan di mana pun Anda berada.</p>
                    </div>
                </div>
            </div>

            {{-- Right Side: Content Banner + Checks --}}
            <div class="security-info-block">
                <span class="security-label"><i class="fas fa-shield-heart"></i> PRIORITAS UTAMA KAMI</span>
                <h2 class="security-title">Keamanan dan Kenyamanan Anda Adalah Prioritas Kami</h2>
                <p class="security-desc">Setiap peralatan yang kami sewakan melewati proses pemeliharaan ketat agar petualangan Anda tetap aman dan tak terlupakan.</p>
                
                <ul class="security-check-list">
                    <li>
                        <span class="check-bullet"><i class="fas fa-check"></i></span>
                        <div>
                            <strong>Pengecekan Kualitas Double-Check</strong>
                            <p>Tim kami memeriksa fisik alat sebelum diserahterimakan untuk menghindari defect di lapangan.</p>
                        </div>
                    </li>
                    <li>
                        <span class="check-bullet"><i class="fas fa-check"></i></span>
                        <div>
                            <strong>Garansi Penggantian Alat Rusak</strong>
                            <p>Apabila terjadi malfungsi teknis di jalan, kami siap mengirimkan alat pengganti dengan segera.</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</div>
@endsection
