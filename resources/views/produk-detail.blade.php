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
        {{-- TOP ROW: 2-COLUMN LAYOUT --}}
        <div class="detail-top-grid">
            {{-- LEFT COLUMN: Main Image & Horizontal Thumbnails --}}
            <div class="left-column">
                <div class="gallery-main">
                    <img src="{{ asset($product->url_gambar ?? 'images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}" id="main-product-img">
                </div>
                <div class="gallery-thumbnails horizontal">
                    <button class="thumb-btn active" onclick="changeImage(this)">
                        <img src="{{ asset($product->url_gambar ?? 'images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}">
                    </button>
                    <button class="thumb-btn" onclick="changeImage(this)">
                        <img src="{{ asset('images/tent-expedition.png') }}" alt="{{ $product->nama_produk }}">
                    </button>
                    <button class="thumb-btn" onclick="changeImage(this)">
                        <img src="{{ asset('images/backpack-product.png') }}" alt="Outdoor Backpack">
                    </button>
                    <button class="thumb-btn" onclick="changeImage(this)">
                        <img src="{{ asset('images/backpack-product.png') }}" alt="Outdoor Backpack 2">
                    </button>
                </div>
            </div>

            {{-- RIGHT COLUMN: Info, Calendar, Actions --}}
            <div class="right-column">
                <div class="product-header-block">
                    <span class="detail-category-badge">{{ $product->category->nama_kategori ?? 'PRO GRADE GEAR' }}</span>
                    <h1 class="detail-product-name">{{ $product->nama_produk }}</h1>
                    <p class="detail-short-desc">{{ $product->deskripsi }}</p>
                </div>

                <hr class="section-divider">

                <div class="price-rating-row">
                    <div class="price-box">
                        <span class="price-box-label">HARGA SEWA</span>
                        <div class="price-box-value">
                            Rp {{ number_format($product->harga_sewa, 0, ',', '.') }}
                            <span class="price-box-unit">/ hari</span>
                        </div>
                    </div>
                    <div class="rating-box">
                        <i class="fas fa-star"></i> {{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '4.9' }}
                        <span class="review-link">({{ $product->reviewCount() }} Ulasan)</span>
                    </div>
                </div>

                <hr class="section-divider">

                {{-- Interactive Rent Calendar Selection --}}
                <div class="calendar-card-section">
                    <div class="calendar-card-header">
                        <h4 id="calendar-month-year">Pilih Tanggal Sewa</h4>
                        <div class="cal-nav-arrows">
                            <i class="fas fa-chevron-left" id="cal-prev-month"></i>
                            <i class="fas fa-chevron-right" id="cal-next-month"></i>
                        </div>
                    </div>
                    <div class="calendar-grid-wrapper">
                        <div class="cal-days-header-row">
                            <span>S</span><span>S</span><span>R</span><span>K</span><span>J</span><span>S</span><span>M</span>
                        </div>
                        <div class="cal-days-grid" id="calendar-days-grid">
                            {{-- Will be generated dynamically via JS --}}
                        </div>
                    </div>
                </div>

                <form method="POST" id="checkout-form" class="checkout-form-block">
                    @csrf
                    <input type="hidden" name="days" id="input-days" value="3">
                    <input type="hidden" name="quantity" id="input-qty" value="1">

                    {{-- Quantity Selector --}}
                    <div class="qty-selector-group">
                        <span class="qty-field-label">Jumlah Unit</span>
                        <div class="qty-control-box">
                            <button type="button" class="btn-qty-adj" id="qty-minus"><i class="fas fa-minus"></i></button>
                            <span class="qty-display-val" id="qty-display-value">1</span>
                            <button type="button" class="btn-qty-adj" id="qty-plus"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    {{-- Rent Summary --}}
                    <div class="rent-summary-box" style="background: #F8F9F8; border: 1px solid var(--border); border-radius: 12px; padding: 20px; margin: 8px 0 4px;">
                        <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-dark); margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.05em;">Ringkasan Estimasi Biaya</h4>
                        <div style="display: flex; justify-content: space-between; font-size: 0.85rem; margin-bottom: 8px;">
                            <span id="summary-dur-text" style="color: var(--text-medium);">Sewa 3 hari x 1 unit</span>
                            <span id="summary-dur-price" style="font-weight: 600; color: var(--text-dark);">Rp 0</span>
                        </div>
                        <hr style="border: none; border-top: 1px solid var(--border); margin: 8px 0;">
                        <div style="display: flex; justify-content: space-between; font-size: 0.95rem; font-weight: 700;">
                            <span style="color: var(--green-dark);">Total Estimasi</span>
                            <span id="summary-total-price" style="color: var(--green-dark);">Rp 0</span>
                        </div>
                    </div>

                    {{-- CTA buttons --}}
                    <div class="cta-actions-group">
                        <button type="submit" formaction="{{ route('cart.store', $product->id) }}" class="btn-cta-cart">
                            <i class="fas fa-shopping-bag"></i> Tambah ke Keranjang
                        </button>
                        <button type="submit" formaction="{{ route('wishlist.toggle', $product->id) }}" class="btn-cta-wishlist">
                            <i class="far fa-heart"></i> Tambah ke Wishlist
                        </button>
                    </div>
                </form>

                <div class="quick-specs">
                    <h4>Spesifikasi Teknik</h4>
                    <div class="quick-specs-grid">
                        @php
                            $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
                            $count = 0;
                        @endphp
                        @foreach($specs as $key => $val)
                            @if($count < 4)
                                <div class="spec-row">
                                    <span class="spec-label">{{ ucwords(str_replace('_', ' ', $key)) }}</span>
                                    <span class="spec-val">{{ $val }}</span>
                                </div>
                                @php $count++; @endphp
                            @endif
                        @endforeach
                        @if(empty($specs))
                            <div class="spec-row">
                                <span class="spec-label">Kapasitas</span>
                                <span class="spec-val">3-4 Orang</span>
                            </div>
                            <div class="spec-row">
                                <span class="spec-label">Berat Total</span>
                                <span class="spec-val">3.8 kg</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
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

    // Dynamic Monthly Calendar implementation
    let currentMonth = new Date().getMonth();
    let currentYear = new Date().getFullYear();
    const todayDate = new Date();
    todayDate.setHours(0,0,0,0);

    let startDate = null;
    let endDate = null;

    const monthNames = ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];

    function generateCalendar(month, year) {
        const grid = document.getElementById('calendar-days-grid');
        const headerText = document.getElementById('calendar-month-year');
        if (!grid || !headerText) return;

        grid.innerHTML = '';
        headerText.innerText = `${monthNames[month]} ${year}`;

        // Get first day of the month
        const firstDay = new Date(year, month, 1).getDay();
        // Convert Sunday as 0 to Sunday as 7, so Monday is 1, Sunday is 7 to align with S-S-R-K-J-S-M
        let startOffset = firstDay === 0 ? 6 : firstDay - 1;

        // Get total days in the month
        const totalDays = new Date(year, month + 1, 0).getDate();

        // Get total days in the previous month
        const prevMonthTotalDays = new Date(year, month, 0).getDate();

        // Add padding from previous month
        for (let i = startOffset; i > 0; i--) {
            const dayNum = prevMonthTotalDays - i + 1;
            const dayDiv = document.createElement('div');
            dayDiv.className = 'cal-day disabled';
            dayDiv.innerText = dayNum;
            grid.appendChild(dayDiv);
        }

        // Add actual days
        for (let i = 1; i <= totalDays; i++) {
            const dayDate = new Date(year, month, i);
            dayDate.setHours(0,0,0,0);

            const dayDiv = document.createElement('div');
            dayDiv.className = 'cal-day';
            dayDiv.innerText = i;
            dayDiv.dataset.date = dayDate.toISOString();

            if (dayDate < todayDate) {
                dayDiv.classList.add('disabled');
            } else {
                dayDiv.addEventListener('click', () => handleDayClick(dayDate));
            }

            // Highlight selected range
            highlightDay(dayDiv, dayDate);

            grid.appendChild(dayDiv);
        }
        
        // Add padding for next month to complete the grid (42 cells / rows)
        const totalCells = startOffset + totalDays;
        const nextMonthPadding = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
        for (let i = 1; i <= nextMonthPadding; i++) {
            const dayDiv = document.createElement('div');
            dayDiv.className = 'cal-day disabled';
            dayDiv.innerText = i;
            grid.appendChild(dayDiv);
        }
    }

    function highlightDay(dayDiv, dayDate) {
        if (startDate && dayDate.getTime() === startDate.getTime()) {
            dayDiv.classList.add('selected', 'active');
        } else if (endDate && dayDate.getTime() === endDate.getTime()) {
            dayDiv.classList.add('selected', 'active');
        } else if (startDate && endDate && dayDate > startDate && dayDate < endDate) {
            dayDiv.classList.add('selected');
        }
    }

    function handleDayClick(date) {
        if (!startDate || (startDate && endDate)) {
            // First click or resetting range
            startDate = date;
            endDate = null;
            document.getElementById('input-days').value = 1;
        } else if (startDate && !endDate) {
            if (date < startDate) {
                // Clicked a date before start date: set as new start date
                startDate = date;
            } else {
                // Set as end date
                endDate = date;
                const diffTime = Math.abs(endDate - startDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1; // inclusive
                document.getElementById('input-days').value = diffDays;
            }
        }
        
        // Re-generate to update visuals
        generateCalendar(currentMonth, currentYear);
        updateSummary();
    }

    document.getElementById('cal-prev-month')?.addEventListener('click', () => {
        currentMonth--;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }
        generateCalendar(currentMonth, currentYear);
    });

    document.getElementById('cal-next-month')?.addEventListener('click', () => {
        currentMonth++;
        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        generateCalendar(currentMonth, currentYear);
    });

    // Initialize calendar and summary on load
    generateCalendar(currentMonth, currentYear);
    updateSummary();
</script>
@endsection
