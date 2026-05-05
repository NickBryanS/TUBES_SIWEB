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

        <form id="checkout-form" action="{{ route('checkout.store') }}" method="POST">
            @csrf
            <!-- Hidden inputs untuk tanggal sewa -->
            <input type="hidden" name="tanggal_mulai" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
            <input type="hidden" name="tanggal_selesai" value="{{ \Carbon\Carbon::now()->addDays($carts->max('days') ?? 1)->format('Y-m-d') }}">
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
                            <p>Jl. Percobaan No. 45, Jakarta Selatan (Dekat Area Parkir Utama)</p>
                            <p class="pickup-hours"><i class="fas fa-clock"></i> Operasional: 08:00 - 20:00 WIB</p>
                        </div>
                    </div>
                </div>

                <!-- PERSONAL INFO -->
                <div class="checkout-section">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NAMA PENERIMA</label>
                            <input type="text" class="form-input" value="John Doe" id="nama-penerima" form="checkout-form">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NOMOR HP</label>
                            <input type="text" class="form-input" value="0812.5456.7890" id="nomor-hp" form="checkout-form">
                        </div>
                    </div>
                </div>

                <!-- DELIVERY ADDRESS (initially hidden, shown when method=deliver) -->
                <div class="checkout-section" id="delivery-address-section" style="display:none;">
                    <h3 class="checkout-section-title">Detail Pengiriman</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">NAMA LENGKAP</label>
                            <input type="text" class="form-input" value="John Doe" form="checkout-form">
                        </div>
                        <div class="form-group">
                            <label class="form-label">NOMOR HP</label>
                            <input type="text" class="form-input" value="0812.5456.7890" form="checkout-form">
                        </div>
                    </div>
                    <div class="form-group full-width">
                        <label class="form-label">ALAMAT LENGKAP</label>
                        <textarea class="form-textarea" name="alamat_pengiriman" rows="3" form="checkout-form">Jl. Rimba No. 12, Jakarta Selatan</textarea>
                    </div>
                </div>

                <!-- IDENTITY UPLOAD (shown when method=deliver) -->
                <div class="checkout-section" id="identity-section" style="display:none;">
                    <h3 class="checkout-section-title">Verifikasi Identitas (Jaminan)</h3>
                    <p class="upload-note">Wajib mengunggah tanda pengenal asli sebagai jaminan pengiriman alat.</p>
                    <div class="upload-area" id="upload-area">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p><strong>Klik untuk Upload atau seret file</strong></p>
                        <span>Upload Foto KTP/SIM (maks. 5MB)</span>
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
                    <div class="summary-line">
                        <span>Ongkos Kirim</span>
                        <span>Gratis</span>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Admin</span>
                        <span>Gratis</span>
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
</script>
@endsection
