@extends('layouts.app')

@section('title', 'Detail Produk - Gardakala Outdoor')
@section('description', 'Detail peralatan outdoor untuk disewa di Gardakala Outdoor.')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/produk-detail.css') }}">
@endsection

@section('content')
<div class="produk-page">
    <div class="produk-container">
        <!-- LEFT: IMAGES -->
        <div class="produk-images">
            <div class="produk-main-image" id="main-product-image">
                <img src="{{ asset('images/tent-expedition.png') }}" alt="Garkadala Expedition X-1" id="main-img">
            </div>
            <div class="produk-thumbnails">
                <button class="thumb active" onclick="changeImage(this)">
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="View 1">
                </button>
                <button class="thumb" onclick="changeImage(this)">
                    <img src="{{ asset('images/tent-product.png') }}" alt="View 2">
                </button>
                <button class="thumb" onclick="changeImage(this)">
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="View 3">
                </button>
                <button class="thumb" onclick="changeImage(this)">
                    <img src="{{ asset('images/tent-product.png') }}" alt="View 4">
                </button>
                <button class="thumb" onclick="changeImage(this)">
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="View 5">
                </button>
            </div>
        </div>

        <!-- RIGHT: DETAILS -->
        <div class="produk-details">
            <span class="produk-badge-label">PRO GRADE GEAR</span>
            <h1 class="produk-name">Garkadala Expedition X-1</h1>
            <p class="produk-desc-short">Tenda ekspedisi 4-musim yang dirancang untuk kondisi cuaca paling ekstrem di pegunungan tinggi.</p>

            <div class="produk-price-section">
                <div class="price-left">
                    <span class="price-label">HARGA SEWA</span>
                    <span class="price-value">Rp 250.000 <span class="price-unit">/hari</span></span>
                </div>
                <div class="price-right">
                    <div class="rating-display">
                        <i class="fas fa-star"></i> 4.3
                    </div>
                    <span class="review-count">(128 Ulasan)</span>
                </div>
            </div>

            <!-- CALENDAR -->
            <div class="calendar-section">
                <div class="calendar-header">
                    <h4>Pilih Tanggal Sewa</h4>
                    <div class="calendar-nav">
                        <button class="cal-nav-btn" id="cal-prev"><i class="fas fa-chevron-left"></i></button>
                        <button class="cal-nav-btn" id="cal-next"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                <div class="calendar-grid">
                    <div class="cal-day-header">M</div>
                    <div class="cal-day-header">S</div>
                    <div class="cal-day-header">S</div>
                    <div class="cal-day-header">R</div>
                    <div class="cal-day-header">K</div>
                    <div class="cal-day-header">J</div>
                    <div class="cal-day-header">S</div>
                    <div class="cal-day disabled">28</div>
                    <div class="cal-day disabled">29</div>
                    <div class="cal-day disabled">30</div>
                    <div class="cal-day">1</div>
                    <div class="cal-day">2</div>
                    <div class="cal-day">3</div>
                    <div class="cal-day">4</div>
                    <div class="cal-day">5</div>
                    <div class="cal-day">6</div>
                    <div class="cal-day">7</div>
                    <div class="cal-day selected">8</div>
                    <div class="cal-day selected">9</div>
                    <div class="cal-day selected active">10</div>
                    <div class="cal-day">11</div>
                    <div class="cal-day">12</div>
                    <div class="cal-day">13</div>
                    <div class="cal-day">14</div>
                    <div class="cal-day">15</div>
                    <div class="cal-day">16</div>
                    <div class="cal-day">17</div>
                    <div class="cal-day">18</div>
                </div>
            </div>

            <!-- QTY -->
            <div class="qty-section">
                <span class="qty-label">Jumlah Unit</span>
                <div class="qty-controls">
                    <button class="qty-btn" id="qty-minus"><i class="fas fa-minus"></i></button>
                    <span class="qty-value" id="qty-value">1</span>
                    <button class="qty-btn" id="qty-plus"><i class="fas fa-plus"></i></button>
                </div>
            </div>

            <!-- ACTIONS -->
            <button class="btn-add-cart" id="add-to-cart">
                Tambah ke Keranjang
            </button>
            <button class="btn-wishlist" id="add-wishlist">
                <i class="far fa-heart"></i> Tambah ke Wishlist
            </button>

            <!-- SPECIFICATIONS -->
            <div class="specs-section">
                <h4 class="specs-title">Spesifikasi Teknik</h4>
                <div class="specs-table">
                    <div class="spec-row">
                        <span class="spec-key">Kapasitas</span>
                        <span class="spec-val">3-4 Orang</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Berat Total</span>
                        <span class="spec-val">3,8 kg</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Material Outer</span>
                        <span class="spec-val">Ripstop Nylon 40D Sil-Poly</span>
                    </div>
                    <div class="spec-row">
                        <span class="spec-key">Water Column</span>
                        <span class="spec-val">5000 mm</span>
                    </div>
                </div>
            </div>

            <!-- FEATURES -->
            <div class="features-section">
                <h4 class="features-title">Fitur Unggulan</h4>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Storm-Proof Design:</strong> Mampu menahan angin hingga kecepatan 80 km/jam.
                    </div>
                </div>
                <div class="feature-item">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Thermal Reflective:</strong> Lapisan dalam memantulkan panas tubuh untuk kehangatan maksimal.
                    </div>
                </div>
            </div>

            <!-- WARRANTY -->
            <div class="warranty-section">
                <div class="warranty-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <h4>Informasi Garansi & Keamanan</h4>
                    <p>Perlengkapan ini terasuransi dan telah mendapatkan profesional termasuk dalam setiap sewa.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function changeImage(thumb) {
    const mainImg = document.getElementById('main-img');
    const imgSrc = thumb.querySelector('img').src;
    mainImg.src = imgSrc;
    document.querySelectorAll('.thumb').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

document.getElementById('qty-minus')?.addEventListener('click', function() {
    const val = document.getElementById('qty-value');
    let current = parseInt(val.textContent);
    if (current > 1) val.textContent = current - 1;
});

document.getElementById('qty-plus')?.addEventListener('click', function() {
    const val = document.getElementById('qty-value');
    let current = parseInt(val.textContent);
    val.textContent = current + 1;
});
</script>
@endsection
