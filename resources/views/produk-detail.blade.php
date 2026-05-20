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
                <img src="{{ asset($product->url_gambar ?? 'images/placeholder.png') }}" alt="{{ $product->nama_produk }}" id="main-img">
            </div>
            <div class="produk-thumbnails">
                <button class="thumb active" onclick="changeImage(this)">
                    <img src="{{ asset($product->url_gambar ?? 'images/placeholder.png') }}" alt="{{ $product->nama_produk }}">
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
                        <i class="fas fa-star" style="color: #fbbf24;"></i> {{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '-' }}
                    </div>
                    <span class="review-count">({{ $product->reviewCount() }} Ulasan)</span>
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
                    @php
                        $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
                    @endphp
                    @forelse($specs as $key => $val)
                    <div class="spec-row">
                        <span class="spec-key">{{ $key }}</span>
                        <span class="spec-val">{{ $val }}</span>
                    </div>
                    @empty
                    <div class="spec-row">
                        <span class="spec-key" style="color: var(--text-light);">Belum ada spesifikasi</span>
                    </div>
                    @endforelse
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

            <!-- REVIEWS SECTION -->
            <div class="reviews-section" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid #eaeaea;">
                <h4 style="font-size: 1.25rem; font-weight: 700; color: #1a1a1a; margin-bottom: 20px;">Ulasan Pelanggan</h4>
                
                @auth
                    <!-- FORM ULASAN -->
                    <div class="review-form-container" style="background: #f9fafb; padding: 20px; border-radius: 12px; margin-bottom: 25px;">
                        <h5 style="margin-bottom: 15px; font-size: 1rem; color: #374151;">Tulis Ulasan Anda</h5>
                        <form action="{{ route('ulasan.store', $product->id) }}" method="POST">
                            @csrf
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #4b5563;">Pilih Rating</label>
                                <div class="star-rating-input" style="display: flex; gap: 5px; flex-direction: row-reverse; justify-content: flex-end;">
                                    <input type="radio" id="star5" name="rating" value="5" style="display: none;" />
                                    <label for="star5" title="5 stars"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" id="star4" name="rating" value="4" style="display: none;" />
                                    <label for="star4" title="4 stars"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" id="star3" name="rating" value="3" style="display: none;" />
                                    <label for="star3" title="3 stars"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" id="star2" name="rating" value="2" style="display: none;" />
                                    <label for="star2" title="2 stars"><i class="fas fa-star"></i></label>
                                    
                                    <input type="radio" id="star1" name="rating" value="1" style="display: none;" required />
                                    <label for="star1" title="1 star"><i class="fas fa-star"></i></label>
                                </div>
                                <style>
                                    .star-rating-input label { font-size: 1.5rem; color: #d1d5db; cursor: pointer; transition: color 0.2s; }
                                    .star-rating-input label:hover,
                                    .star-rating-input label:hover ~ label,
                                    .star-rating-input input[type="radio"]:checked ~ label { color: #fbbf24; }
                                </style>
                            </div>
                            <div style="margin-bottom: 15px;">
                                <label style="display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 8px; color: #4b5563;">Komentar (Opsional)</label>
                                <textarea name="ulasan" rows="3" style="width: 100%; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px; font-family: inherit; font-size: 0.9rem;" placeholder="Bagaimana pengalaman Anda menggunakan produk ini?"></textarea>
                            </div>
                            <button type="submit" style="background: #2563eb; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: background 0.2s;">Kirim Ulasan</button>
                        </form>
                    </div>
                @else
                    <div style="background: #f3f4f6; padding: 15px; border-radius: 8px; margin-bottom: 25px; font-size: 0.9rem; color: #4b5563;">
                        Silakan <a href="{{ route('login') }}" style="color: #2563eb; font-weight: 600;">login</a> untuk memberikan ulasan.
                    </div>
                @endauth

                <!-- DAFTAR ULASAN -->
                <div class="reviews-list">
                    @forelse($product->reviews()->latest()->get() as $review)
                        <div class="review-card" style="padding-bottom: 15px; margin-bottom: 15px; border-bottom: 1px solid #f3f4f6;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                <div style="font-weight: 600; font-size: 0.95rem; color: #1f2937;">{{ $review->user->nama_lengkap ?? $review->user->email }}</div>
                                <div style="color: #fbbf24; font-size: 0.85rem;">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star" style="{{ $i <= $review->rating ? 'color: #fbbf24;' : 'color: #e5e7eb;' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <div style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 8px;">{{ $review->created_at->diffForHumans() }}</div>
                            @if($review->ulasan)
                                <p style="font-size: 0.9rem; color: #4b5563; line-height: 1.5;">{{ $review->ulasan }}</p>
                            @endif
                        </div>
                    @empty
                        <p style="color: #6b7280; font-size: 0.9rem;">Belum ada ulasan untuk produk ini. Jadilah yang pertama!</p>
                    @endforelse
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
