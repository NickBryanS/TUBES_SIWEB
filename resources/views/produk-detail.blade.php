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
            <span class="produk-badge-label">{{ $product->category->nama_kategori ?? 'GEAR' }}</span>
            <h1 class="produk-name">{{ $product->nama_produk }}</h1>
            <p class="produk-desc-short">{{ $product->deskripsi }}</p>

            <div class="produk-price-section">
                <div class="price-left">
                    <span class="price-label">HARGA SEWA</span>
                    <span class="price-value">Rp {{ number_format($product->harga_sewa, 0, ',', '.') }} <span class="price-unit">/hari</span></span>
                </div>
                <div class="price-right">
                    <div class="rating-display">
                        <i class="fas fa-star"></i> 4.5
                    </div>
                    <span class="review-count">(128 Ulasan)</span>
                </div>
            </div>

            <form method="POST" id="action-form">
                @csrf
                <input type="hidden" name="days" id="input-days" value="3">
                <input type="hidden" name="quantity" id="input-qty" value="1">
                
                <!-- CALENDAR -->
                <div class="calendar-section">
                    <div class="calendar-header">
                        <h4>Pilih Tanggal Sewa</h4>
                        <div class="calendar-nav">
                            <button type="button" class="cal-nav-btn" id="cal-prev"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="cal-nav-btn" id="cal-next"><i class="fas fa-chevron-right"></i></button>
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
                        <button type="button" class="qty-btn" id="qty-minus"><i class="fas fa-minus"></i></button>
                        <span class="qty-value" id="qty-value">1</span>
                        <button type="button" class="qty-btn" id="qty-plus"><i class="fas fa-plus"></i></button>
                    </div>
                </div>

                <!-- ACTIONS -->
                @if(session('success'))
                    <div class="alert alert-success" style="padding:10px; background:#d4edda; color:#155724; border-radius:5px; margin-bottom:15px; font-size:14px;">
                        {{ session('success') }}
                    </div>
                @endif
                
                <div style="display: flex; gap: 10px; margin-bottom: 10px;">
                    <button type="submit" formaction="{{ route('cart.store', $product->id) }}" class="btn-add-cart" id="add-to-cart" style="flex: 1;">
                        Tambah ke Keranjang
                    </button>
                    <button type="submit" formaction="{{ route('cart.directCheckout', $product->id) }}" class="btn-add-cart" style="flex: 1; background: #e63946; color: white;">
                        Checkout
                    </button>
                </div>
                
                <button type="submit" formaction="{{ route('wishlist.toggle', $product->id) }}" class="btn-wishlist" id="add-wishlist">
                    <i class="far fa-heart"></i> Tambah ke Wishlist
                </button>
            </form>

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
    const inputQty = document.getElementById('input-qty');
    let current = parseInt(val.textContent);
    if (current > 1) {
        val.textContent = current - 1;
        if(inputQty) inputQty.value = current - 1;
    }
});

document.getElementById('qty-plus')?.addEventListener('click', function() {
    const val = document.getElementById('qty-value');
    const inputQty = document.getElementById('input-qty');
    let current = parseInt(val.textContent);
    val.textContent = current + 1;
    if(inputQty) inputQty.value = current + 1;
});

// Calendar range selection
let calStartDate = null;
const calDays = Array.from(document.querySelectorAll('.cal-day:not(.disabled)'));

calDays.forEach((day, index) => {
    day.addEventListener('click', function() {
        if (!calStartDate || document.querySelectorAll('.cal-day.active').length === 2) {
            // First click (or reset): clear everything
            calDays.forEach(d => { d.classList.remove('selected', 'active'); });
            this.classList.add('selected', 'active');
            calStartDate = index;
            document.getElementById('input-days').value = 1;
        } else {
            // Second click: create range
            let startIdx = Math.min(calStartDate, index);
            let endIdx = Math.max(calStartDate, index);
            
            calDays.forEach((d, i) => {
                if (i >= startIdx && i <= endIdx) {
                    d.classList.add('selected');
                }
                if (i === startIdx || i === endIdx) {
                    d.classList.add('active');
                }
            });
            
            const numDays = endIdx - startIdx + 1;
            const inputDays = document.getElementById('input-days');
            if (inputDays) {
                inputDays.value = numDays;
            }
            calStartDate = null; // Reset for next interaction if needed
        }
    });
});
</script>
@endsection
