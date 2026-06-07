@extends('layouts.app')

@section('title', 'Checkout - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
@endsection

@section('content')
<div class="checkout-page">
    <div class="checkout-container">
        {{-- STEPPER (partial) --}}
        @include('partials.checkout-stepper', ['currentStep' => 1])

        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
        </form>

        <div class="checkout-grid">
            <!-- LEFT -->
            <div class="checkout-left">
                <!-- ITEMS -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title"><i class="fas fa-box"></i> Item Sewa</h3>
                    
                    @foreach($carts as $cart)
                    <div class="checkout-item">
                        <div class="checkout-item-img">
                            <img src="{{ $cart->product->url_gambar ?? asset('images/default.png') }}" alt="{{ $cart->product->nama_produk }}">
                        </div>
                        <div class="checkout-item-info">
                            <h4>{{ $cart->product->nama_produk }}</h4>
                            <p class="item-period">Sewa: {{ $cart->days }} Hari</p>
                            <p class="item-price">Rp {{ number_format($cart->product->harga_sewa * $cart->quantity * $cart->days, 0, ',', '.') }}</p>
                        </div>
                        <div class="item-qty-controls">
                            <form action="{{ route('cart.update', $cart->id) }}" method="POST" style="display:flex; align-items:center; gap:5px; margin:0;">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="days" value="{{ $cart->days }}">
                                <button type="submit" name="quantity" value="{{ max(1, $cart->quantity - 1) }}" class="qty-sm-btn"><i class="fas fa-minus"></i></button>
                                <span>{{ $cart->quantity }}</span>
                                <button type="submit" name="quantity" value="{{ $cart->quantity + 1 }}" class="qty-sm-btn"><i class="fas fa-plus"></i></button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- RENTAL DATES -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title"><i class="fas fa-calendar-alt"></i> Tanggal Sewa</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">TANGGAL MULAI</label>
                            <input type="date" class="form-input" name="tanggal_mulai" id="tanggal-mulai" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" form="checkout-form" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">TANGGAL SELESAI</label>
                            <input type="date" class="form-input" name="tanggal_selesai" id="tanggal-selesai" value="{{ \Carbon\Carbon::now()->addDays($carts->max('days') ?? 1)->format('Y-m-d') }}" min="{{ \Carbon\Carbon::now()->addDay()->format('Y-m-d') }}" form="checkout-form" readonly required style="background-color: #f1f3f1; cursor: not-allowed;">
                        </div>
                    </div>
                    <small style="color: #666; font-size: 0.8rem; margin-top: 8px; display: block; line-height: 1.4;">
                        * Tanggal selesai disesuaikan otomatis dengan durasi sewa terlama di keranjang Anda ({{ $carts->max('days') ?? 1 }} hari).
                    </small>
                </div>

                <!-- METHOD -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title">Metode Pemenuhan</h3>
                    <div class="method-options">
                        <label class="method-card" id="method-deliver">
                            <input type="radio" name="metode_pengambilan" value="deliver" form="checkout-form">
                            <div class="method-content">
                                <i class="fas fa-truck"></i>
                                <div>
                                    <strong>Kirim ke Alamat</strong>
                                    <span>Pengiriman kurir terdekat</span>
                                </div>
                            </div>
                        </label>
                        <label class="method-card selected" id="method-pickup">
                            <input type="radio" name="metode_pengambilan" value="pickup" checked form="checkout-form">
                            <div class="method-content">
                                <i class="fas fa-store"></i>
                                <div>
                                    <strong>Ambil di Basecamp</strong>
                                    <span>Gardakala outdoor</span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PICKUP INFO -->
                <div class="checkout-section" id="pickup-info">
                    <h3 class="checkout-section-title">Informasi Pengambilan</h3>
                    <div class="pickup-location">
                        <div class="pickup-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <div>
                            <strong>Basecamp GKDL Outdoor</strong>
                            <p>Jl. Raya Soreang - Banjaran No.216, RT.02/RW.01, Ciluncat, Kec. Cangkuang, Kabupaten Bandung, Jawa Barat 40238</p>
                            <p class="pickup-hours"><i class="fas fa-clock"></i> Operasional: 08:00 - 21:00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- PERSONAL INFO -->
                <div class="checkout-section">
                    <h3 class="checkout-section-title"><i class="fas fa-user"></i> Info Penerima</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NAMA PENERIMA</label>
                            <input type="text" class="form-input" name="nama_penerima" value="{{ Auth::user()->nama_lengkap ?? '' }}" id="nama-penerima" form="checkout-form" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">NOMOR HP</label>
                            <input type="text" class="form-input" name="telepon_penerima" value="{{ Auth::user()->nomor_telepon ?? '' }}" id="nomor-hp" form="checkout-form" required>
                        </div>
                    </div>
                </div>

                <!-- DELIVERY ADDRESS (initially hidden, shown when method=deliver) -->
                <div class="checkout-section" id="delivery-address-section" style="display:none;">
                    <h3 class="checkout-section-title">Detail Pengiriman</h3>
                    <div class="form-group full-width">
                        <label class="form-label">ALAMAT LENGKAP</label>
                        <textarea class="form-textarea" name="alamat_pengiriman" rows="3" form="checkout-form" placeholder="Masukkan alamat lengkap pengiriman..."></textarea>
                    </div>
                    <div class="form-group full-width" style="margin-top: 15px;">
                        <label class="form-label">JARAK TEMPUH (KM)</label>
                        <input type="number" step="0.1" min="0" class="form-input" name="jarak_tempuh" id="jarak-tempuh" form="checkout-form" placeholder="Masukkan jarak tempuh dari basecamp ke alamat Anda...">
                        <small style="color: #666; font-size: 0.8rem; margin-top: 5px; display: block;">Biaya pengiriman Rp 5.000 / km.</small>
                    </div>
                </div>

                <!-- IDENTITY UPLOAD (shown when method=deliver) -->
                <div class="checkout-section" id="identity-section" style="display:none;">
                    <h3 class="checkout-section-title">Verifikasi Identitas (Jaminan)</h3>
                    <p class="upload-note">Wajib mengunggah tanda pengenal asli sebagai jaminan pengiriman alat.</p>
                    <div class="upload-area" id="upload-area" onclick="document.getElementById('foto_ktp').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p id="upload-ktp-text"><strong>Klik untuk Upload atau seret file</strong></p>
                        <span>Upload Foto KTP/SIM (maks. 5MB)</span>
                        <input type="file" name="foto_ktp" id="foto_ktp" form="checkout-form" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                    </div>
                </div>
            </div>

            <!-- RIGHT: SUMMARY -->
            <div class="checkout-right">
                <div class="checkout-summary">
                    <h3>Ringkasan Pesanan</h3>
                    <div class="summary-line">
                        <span>Subtotal Alat</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>

                    <div class="summary-total-line">
                        <span>Total Pembayaran</span>
                        <span class="summary-total-price">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="return-notice">
                        <i class="fas fa-info-circle"></i>
                        <span>Pastikan wajib mengembalikan alat dalam keadaan bersih sesuai jadwal.</span>
                    </div>

                    <button type="submit" form="checkout-form" class="btn-proceed" id="btn-proceed" style="width: 100%; border: none; cursor: pointer;">
                        LANJUT KE PEMBAYARAN
                    </button>
                </div>

                <div class="secure-badge">
                    <i class="fas fa-lock"></i> SECURE CHECKOUT
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('input[name="metode_pengambilan"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('selected'));
        this.closest('.method-card').classList.add('selected');
        
        const pickup = document.getElementById('pickup-info');
        const delivery = document.getElementById('delivery-address-section');
        const identity = document.getElementById('identity-section');
        
        if (this.value === 'pickup') {
            pickup.style.display = '';
            delivery.style.display = 'none';
            identity.style.display = 'none';
        } else {
            pickup.style.display = 'none';
            delivery.style.display = '';
            identity.style.display = '';
        }
    });
});

// Automatically update tanggal_selesai based on selected tanggal_mulai and cart duration
const tanggalMulaiInput = document.getElementById('tanggal-mulai');
const tanggalSelesaiInput = document.getElementById('tanggal-selesai');
const cartMaxDays = {{ $carts->max('days') ?? 1 }};

if (tanggalMulaiInput && tanggalSelesaiInput) {
    tanggalMulaiInput.addEventListener('change', function() {
        const startDate = new Date(this.value);
        if (!isNaN(startDate.getTime())) {
            startDate.setDate(startDate.getDate() + cartMaxDays);
            
            const year = startDate.getFullYear();
            const month = String(startDate.getMonth() + 1).padStart(2, '0');
            const day = String(startDate.getDate()).padStart(2, '0');
            
            tanggalSelesaiInput.value = `${year}-${month}-${day}`;
        }
    });
}

// File upload preview
document.getElementById('foto_ktp')?.addEventListener('change', function() {
    var textEl = document.getElementById('upload-ktp-text');
    if (this.files.length > 0) {
        textEl.innerHTML = '<strong><i class="fas fa-check-circle" style="color:var(--green-dark)"></i> ' + this.files[0].name + '</strong>';
        document.getElementById('upload-area').style.borderColor = 'var(--green-dark)';
        document.getElementById('upload-area').style.background = 'rgba(45,90,39,0.03)';
    } else {
        textEl.innerHTML = '<strong>Klik untuk Upload atau seret file</strong>';
        document.getElementById('upload-area').style.borderColor = '';
        document.getElementById('upload-area').style.background = '';
    }
});

// Validation on submit
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    let method = document.querySelector('input[name="metode_pengambilan"]:checked').value;
    if (method === 'deliver') {
        let foto = document.getElementById('foto_ktp').files.length;
        if (foto === 0) {
            e.preventDefault();
            alert('Peringatan: Anda harus mengupload bukti jaminan (KTP/SIM) untuk metode pengiriman ke alamat.');
            return false;
        }
    }
});
</script>
@endsection
