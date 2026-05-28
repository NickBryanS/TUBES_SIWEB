@extends('layouts.app')

@section('title', $product->nama_produk . ' - Gardakala Outdoor')
@section('description', 'Sewa ' . $product->nama_produk . ' premium dengan harga terbaik di Gardakala Outdoor.')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/produk-detail.css') }}">
@endsection

@section('content')
<div class="produk-detail-page">
    <div class="produk-detail-container">
        {{-- TOP ROW: GALLERIES & INFOS & SIDEBAR --}}
        <div class="detail-top-grid">
            {{-- 1. LEFT GALLERY: Vertical Thumbnails + Main Image --}}
            <div class="gallery-wrapper">
                <div class="gallery-thumbnails">
                    <button class="thumb-btn active" onclick="changeImage(this)">
                        <img src="{{ asset($product->url_gambar ?? 'images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}">
                    </button>
                    {{-- Additional dynamic/mockup gallery thumbnails for outdoor feel --}}
                    <button class="thumb-btn" onclick="changeImage(this)">
                        <img src="{{ asset('images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}">
                    </button>
                    <button class="thumb-btn" onclick="changeImage(this)">
                        <img src="{{ asset('images/backpack-product.png') }}" alt="Outdoor Backpack">
                    </button>
                </div>
                <div class="gallery-main">
                    <span class="detail-category-badge">{{ $product->category->nama_kategori ?? 'Peralatan' }}</span>
                    <img src="{{ asset($product->url_gambar ?? 'images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}" id="main-product-img">
                </div>
            </div>

            {{-- 2. CENTER CONTENT: Product specifications & Calendar selector --}}
            <div class="info-wrapper">
                <div class="product-header-block">
                    <h1 class="detail-product-name">{{ $product->nama_produk }}</h1>
                    <div class="detail-rating-row">
                        <span class="star-rating">
                            <i class="fas fa-star"></i> {{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '4.8' }}
                        </span>
                        <span class="review-link">{{ $product->reviewCount() }} Ulasan Pelanggan</span>
                        <span class="divider-dot">•</span>
                        <span class="stock-status {{ $product->stok_tersedia > 0 ? 'instock' : 'outstock' }}">
                            {{ $product->stok_tersedia > 0 ? 'Tersedia (' . $product->stok_tersedia . ' Unit)' : 'Stok Habis' }}
                        </span>
                    </div>
                </div>

                <p class="detail-short-desc">{{ $product->deskripsi }}</p>

                {{-- Interactive Rent Calendar Selection --}}
                <div class="calendar-card-section">
                    <div class="calendar-card-header">
                        <h4>Pilih Tanggal Rental</h4>
                        <span class="calendar-hint">Klik tanggal mulai & tanggal berakhir</span>
                    </div>
                    <div class="calendar-grid-wrapper">
                        <div class="cal-days-header-row">
                            <span>Min</span><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span>
                        </div>
                        <div class="cal-days-grid" id="calendar-days-grid">
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
                            <div class="cal-day">19</div>
                            <div class="cal-day">20</div>
                            <div class="cal-day">21</div>
                            <div class="cal-day">22</div>
                            <div class="cal-day">23</div>
                            <div class="cal-day">24</div>
                            <div class="cal-day">25</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. RIGHT SIDEBAR: Pricing, Quantity selection, and CTA Buttons --}}
            <aside class="actions-sidebar">
                <div class="price-box">
                    <span class="price-box-label">Harga Sewa</span>
                    <div class="price-box-value">
                        Rp {{ number_format($product->harga_sewa, 0, ',', '.') }}
                        <span class="price-box-unit">/ hari</span>
                    </div>
                </div>

                <form method="POST" id="checkout-form" class="checkout-form-block">
                    @csrf
                    <input type="hidden" name="days" id="input-days" value="3">
                    <input type="hidden" name="quantity" id="input-qty" value="1">

                    {{-- Quantity Selector --}}
                    <div class="qty-selector-group">
                        <span class="qty-field-label">Jumlah Perlengkapan</span>
                        <div class="qty-control-box">
                            <button type="button" class="btn-qty-adj" id="qty-minus"><i class="fas fa-minus"></i></button>
                            <span class="qty-display-val" id="qty-display-value">1</span>
                            <button type="button" class="btn-qty-adj" id="qty-plus"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    {{-- Dynamic Summary Calculation Box --}}
                    <div class="summary-calc-box">
                        <div class="calc-row">
                            <span id="summary-dur-text">Sewa 3 hari</span>
                            <span id="summary-dur-price">Rp {{ number_format($product->harga_sewa * 3, 0, ',', '.') }}</span>
                        </div>
                        <div class="calc-row border-top">
                            <strong>Total Perkiraan</strong>
                            <strong id="summary-total-price" class="total-text-green">Rp {{ number_format($product->harga_sewa * 3, 0, ',', '.') }}</strong>
                        </div>
                    </div>

                    @if(session('success'))
                        <div class="success-alert-badge">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    {{-- CTA buttons --}}
                    <div class="cta-actions-group">
                        <button type="submit" formaction="{{ route('cart.store', $product->id) }}" class="btn-cta-cart">
                            <i class="fas fa-shopping-bag"></i> + Keranjang
                        </button>
                        <button type="submit" formaction="{{ route('cart.directCheckout', $product->id) }}" class="btn-cta-checkout">
                            Sewa Sekarang
                        </button>
                    </div>

                    {{-- Wishlist Toggle --}}
                    <button type="submit" formaction="{{ route('wishlist.toggle', $product->id) }}" class="btn-cta-wishlist">
                        <i class="far fa-heart"></i> Simpan ke Wishlist
                    </button>
                </form>

                {{-- Guarantee Policy Box --}}
                <div class="guarantee-policy-card">
                    <div class="policy-item">
                        <i class="fas fa-shield-halved"></i>
                        <div>
                            <h5>Alat Higienis & Steril</h5>
                            <p>Dibersihkan dengan disinfektan profesional setelah setiap sewa.</p>
                        </div>
                    </div>
                    <div class="policy-item">
                        <i class="fas fa-rotate-left"></i>
                        <div>
                            <h5>Jaminan Penggantian</h5>
                            <p>Rusak saat di jalan? Kami ganti alat serupa gratis.</p>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        {{-- BOTTOM TAB PANEL --}}
        <div class="detail-bottom-tabs">
            {{-- Tab Headers --}}
            <div class="tab-headers-row">
                <button class="tab-header-btn active" onclick="switchDetailTab(event, 'tab-deskripsi')">Deskripsi</button>
                <button class="tab-header-btn" onclick="switchDetailTab(event, 'tab-spesifikasi')">Spesifikasi Lengkap</button>
                <button class="tab-header-btn" onclick="switchDetailTab(event, 'tab-ulasan')">Ulasan ({{ $product->reviewCount() }})</button>
                <button class="tab-header-btn" onclick="switchDetailTab(event, 'tab-faq')">FAQ Rental</button>
            </div>

            {{-- Tab Contents --}}
            <div class="tab-contents-container">
                {{-- 1. Deskripsi Tab --}}
                <div class="tab-content-panel active" id="tab-deskripsi">
                    <div class="description-rich-text">
                        <h3>Deskripsi Produk</h3>
                        <p>{{ $product->deskripsi }}</p>
                        <br>
                        <h4>Mengapa Memilih Perlengkapan Ini?</h4>
                        <p>Didesain khusus untuk para petualang yang mementingkan keamanan, kehandalan, dan keringanan di medan berat. Setiap jahitan, bahan material, dan fitur telah diuji secara teliti untuk kenyamanan optimal Anda di alam bebas.</p>
                    </div>
                </div>

                {{-- 2. Spesifikasi Lengkap Tab --}}
                <div class="tab-content-panel" id="tab-spesifikasi">
                    <div class="specs-table-wrapper">
                        <h3>Spesifikasi Teknik Lengkap</h3>
                        <div class="detail-specs-table">
                            @php
                                $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
                            @endphp
                            @forelse($specs as $key => $val)
                                <div class="detail-specs-row">
                                    <span class="detail-spec-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="detail-spec-value">{{ $val }}</span>
                                </div>
                            @empty
                                <div class="detail-specs-row empty-row">
                                    <span>Belum ada spesifikasi teknis spesifik untuk produk ini.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- 3. Ulasan Tab --}}
                <div class="tab-content-panel" id="tab-ulasan">
                    <div class="ulasan-panel-grid">
                        <div class="ulasan-form-box">
                            @auth
                                <h4>Tulis Ulasan</h4>
                                <p>Bagikan pengalaman Anda saat menggunakan alat ini.</p>
                                <form action="{{ route('ulasan.store', $product->id) }}" method="POST" class="ulasan-form-control">
                                    @csrf
                                    <div class="form-group-item">
                                        <label>Beri Rating Bintang</label>
                                        <div class="star-rating-radio-group">
                                            <input type="radio" id="star5" name="rating" value="5">
                                            <label for="star5"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star4" name="rating" value="4">
                                            <label for="star4"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star3" name="rating" value="3">
                                            <label for="star3"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star2" name="rating" value="2">
                                            <label for="star2"><i class="fas fa-star"></i></label>
                                            <input type="radio" id="star1" name="rating" value="1" required>
                                            <label for="star1"><i class="fas fa-star"></i></label>
                                        </div>
                                    </div>
                                    <div class="form-group-item">
                                        <label>Pesan Komentar</label>
                                        <textarea name="ulasan" rows="4" placeholder="Apakah tenda bocor? Bagaimana dengan suspensi tas? Tulis di sini..."></textarea>
                                    </div>
                                    <button type="submit" class="btn-submit-ulasan">Kirim Ulasan</button>
                                </form>
                            @else
                                <div class="ulasan-login-notice">
                                    <i class="fas fa-circle-info"></i>
                                    <span>Silakan <a href="{{ route('login') }}">login</a> untuk menulis ulasan produk.</span>
                                </div>
                            @endauth
                        </div>

                        <div class="ulasan-list-box">
                            <h4>Daftar Ulasan Pelanggan</h4>
                            <div class="ulasan-cards-wrapper">
                                @forelse($product->reviews()->latest()->get() as $review)
                                    <div class="ulasan-review-card">
                                        <div class="ulasan-card-header">
                                            <div>
                                                <h5>{{ $review->user->nama_lengkap ?? $review->user->email }}</h5>
                                                <small class="ulasan-time">{{ $review->created_at->diffForHumans() }}</small>
                                            </div>
                                            <div class="stars-display-green">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star" style="{{ $i <= $review->rating ? 'color: var(--yellow-soft);' : 'color: #E2E8F0;' }}"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->ulasan)
                                            <p class="ulasan-card-comment">{{ $review->ulasan }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="empty-reviews-notice">
                                        <i class="far fa-star"></i>
                                        <p>Belum ada ulasan untuk perlengkapan ini. Jadilah yang pertama memberikan review!</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 4. FAQ Rental Tab --}}
                <div class="tab-content-panel" id="tab-faq">
                    <div class="faq-list-wrapper">
                        <h3>Pertanyaan Terkait Penyewaan</h3>
                        <div class="faq-grid-panel">
                            <div class="faq-item-card">
                                <h5>Bagaimana jika alat kotor saat dikembalikan?</h5>
                                <p>Kami mengerti bahwa berpetualang membuat alat terkena lumpur/debu. Jangan khawatir, biaya pencucian standar sudah termasuk dalam tarif sewa. Kecuali noda ekstrem/kerusakan fisik berat.</p>
                            </div>
                            <div class="faq-item-card">
                                <h5>Berapa batas waktu denda keterlambatan?</h5>
                                <p>Keterlambatan pengembalian dikenakan denda sesuai tarif harian produk yang berlaku per hari keterlambatan. Harap hubungi customer support kami jika terjadi keterlambatan darurat di jalan.</p>
                            </div>
                            <div class="faq-item-card">
                                <h5>Dapatkah saya membatalkan sewa yang sudah dibayar?</h5>
                                <p>Pembatalan yang diajukan minimal 24 jam sebelum tanggal mulai rental akan mendapatkan refund penuh 100%. Pembatalan kurang dari 24 jam dikenakan denda pembatalan 50%.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function changeImage(thumb) {
        const mainImg = document.getElementById('main-product-img');
        const imgSrc = thumb.querySelector('img').src;
        mainImg.src = imgSrc;
        document.querySelectorAll('.thumb-btn').forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    }

    function switchDetailTab(event, tabId) {
        document.querySelectorAll('.tab-content-panel').forEach(panel => panel.classList.remove('active'));
        document.querySelectorAll('.tab-header-btn').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById(tabId).classList.add('active');
        event.currentTarget.classList.add('active');
    }

    const pricePerDay = {{ $product->harga_sewa }};

    function updateSummary() {
        const days = parseInt(document.getElementById('input-days').value) || 1;
        const qty = parseInt(document.getElementById('input-qty').value) || 1;
        const total = pricePerDay * days * qty;
        
        const formatter = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            maximumFractionDigits: 0
        });

        const formattedTotal = formatter.format(total).replace("IDR", "Rp");
        const formattedBase = formatter.format(pricePerDay * days).replace("IDR", "Rp");

        document.getElementById('summary-dur-text').innerText = `Sewa ${days} hari x ${qty} unit`;
        document.getElementById('summary-dur-price').innerText = formattedTotal;
        document.getElementById('summary-total-price').innerText = formattedTotal;
    }

    document.getElementById('qty-minus')?.addEventListener('click', function() {
        const val = document.getElementById('qty-display-value');
        const inputQty = document.getElementById('input-qty');
        let current = parseInt(val.textContent);
        if (current > 1) {
            val.textContent = current - 1;
            if(inputQty) inputQty.value = current - 1;
            updateSummary();
        }
    });

    document.getElementById('qty-plus')?.addEventListener('click', function() {
        const val = document.getElementById('qty-display-value');
        const inputQty = document.getElementById('input-qty');
        let current = parseInt(val.textContent);
        if (current < {{ $product->stok_tersedia }}) {
            val.textContent = current + 1;
            if(inputQty) inputQty.value = current + 1;
            updateSummary();
        }
    });

    // Calendar selection range handler
    let calStartDate = null;
    const calDays = Array.from(document.querySelectorAll('.cal-day:not(.disabled)'));

    calDays.forEach((day, index) => {
        day.addEventListener('click', function() {
            if (!calStartDate || document.querySelectorAll('.cal-day.active').length === 2) {
                // First click: reset and activate single day
                calDays.forEach(d => { d.classList.remove('selected', 'active'); });
                this.classList.add('selected', 'active');
                calStartDate = index;
                document.getElementById('input-days').value = 1;
                updateSummary();
            } else {
                // Second click: create date range highlight
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
                document.getElementById('input-days').value = numDays;
                updateSummary();
                calStartDate = null; // reset anchor
            }
        });
    });
</script>
@endsection
