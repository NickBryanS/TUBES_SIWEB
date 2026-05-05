@extends('layouts.app')

@section('title', 'Metode Pembayaran - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
<link rel="stylesheet" href="{{ asset('css/pembayaran.css') }}">
@endsection

@section('content')
<div class="checkout-page">
    <div class="checkout-container">
        {{-- STEPPER (partial) --}}
        @include('partials.checkout-stepper', ['currentStep' => 2])

        <form id="pembayaran-form" action="{{ route('pembayaran.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" value="transfer_bank">
        </form>

        <div class="checkout-grid">
            <!-- LEFT: PAYMENT OPTIONS -->
            <div class="checkout-left">
                <h3 class="checkout-section-title">Metode Pembayaran</h3>

                <!-- Payment Tabs -->
                <div class="payment-tabs" id="payment-tabs">
                    <button class="payment-tab active" data-tab="transfer" data-value="transfer_bank">
                        <i class="fas fa-university"></i> Transfer Bank
                    </button>
                    <button class="payment-tab" data-tab="qris" data-value="qris">
                        <i class="fas fa-qrcode"></i> QRIS
                    </button>
                    <button class="payment-tab" data-tab="cod" data-value="bayar_di_toko">
                        <i class="fas fa-store"></i> Bayar di Toko
                    </button>
                </div>

                <!-- Transfer Bank Content -->
                <div class="payment-content" id="tab-transfer">
                    <div class="bank-info-card">
                        <div class="bank-header">
                            <span class="bank-label">NOMOR REKENING</span>
                        </div>
                        <div class="bank-number-row">
                            <div class="bank-icon"><i class="fas fa-university"></i></div>
                            <div>
                                <span class="bank-number">123-456-7890</span>
                                <span class="bank-name">a/n GKDL OUTDOOR</span>
                            </div>
                            <button type="button" class="copy-btn" id="copy-bank"><i class="fas fa-copy"></i> Salin Nomor</button>
                        </div>
                    </div>

                    <h4 class="upload-title">Upload Bukti Transfer</h4>
                    <div class="upload-area" id="upload-proof" onclick="document.getElementById('bukti-file').click()">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p id="upload-text"><strong>Klik atau seret file ke sini</strong></p>
                        <span>Format: JPG, PNG, PDF (maks. 5MB). Pastikan detail transfer terlihat jelas.</span>
                        <input type="file" name="bukti_pembayaran" id="bukti-file" form="pembayaran-form" accept=".jpg,.jpeg,.png,.pdf" style="display:none;">
                    </div>

                    <div class="payment-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Pesanan Anda akan diverifikasi secara manual oleh tim kami dalam waktu maksimal 1x24 jam setelah bukti transfer diunggah.</span>
                    </div>
                </div>

                <!-- QRIS Content -->
                <div class="payment-content" id="tab-qris" style="display:none;">
                    <div class="qris-info">
                        <div class="qris-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <p>Scan QR code di bawah untuk melakukan pembayaran</p>
                        </div>
                    </div>
                </div>

                <!-- COD Content -->
                <div class="payment-content" id="tab-cod" style="display:none;">
                    <div class="cod-info">
                        <div class="cod-icon"><i class="fas fa-store"></i></div>
                        <div>
                            <h4>Bayar di Toko (Hanya untuk Ambil di Tempat)</h4>
                            <p>Silahkan selesaikan pesanan Anda dan lakukan pembayaran saat pengambilan alat di Basecamp GKDL Outdoor.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="checkout-right">
                <div class="checkout-summary">
                    <h3>RINGKASAN PESANAN</h3>
                    <div class="summary-items">
                        @foreach($carts as $cart)
                        <div class="summary-item">
                            <div>
                                <span class="si-name">{{ $cart->product->nama_produk }}</span>
                                <span class="si-detail">{{ $cart->quantity }} Unit &middot; {{ $cart->days }} Hari</span>
                            </div>
                            <span class="si-price">Rp {{ number_format($cart->product->harga_sewa * $cart->quantity * $cart->days, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Admin</span>
                        <span>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</span>
                    </div>
                    <div class="summary-total-line">
                        <span>Total</span>
                        <span class="summary-total-price">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <button type="submit" form="pembayaran-form" class="btn-proceed btn-create-order" id="btn-create-order" style="width:100%; border:none; cursor:pointer;">
                        BUAT PESANAN <i class="fas fa-arrow-right"></i>
                    </button>
                </div>

                <!-- Promo Banner -->
                <div class="promo-banner">
                    <img src="{{ asset('images/mountain-adventure.png') }}" alt="Promo" onerror="this.parentElement.style.background='var(--green-dark)'; this.style.display='none';">
                    <div class="promo-overlay">
                        <p>"Explore the wild with the best gear."</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Payment tab switching
document.querySelectorAll('.payment-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.payment-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + this.dataset.tab).style.display = '';
        
        // Update hidden metode_pembayaran field
        document.getElementById('metode_pembayaran').value = this.dataset.value;
    });
});

// Copy bank number
document.getElementById('copy-bank')?.addEventListener('click', function() {
    navigator.clipboard.writeText('1234567890');
    this.innerHTML = '<i class="fas fa-check"></i> Disalin!';
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-copy"></i> Salin Nomor';
    }, 2000);
});

// File upload preview
document.getElementById('bukti-file')?.addEventListener('change', function() {
    var textEl = document.getElementById('upload-text');
    if (this.files.length > 0) {
        textEl.innerHTML = '<strong><i class="fas fa-check-circle" style="color:var(--green-dark)"></i> ' + this.files[0].name + '</strong>';
        document.getElementById('upload-proof').style.borderColor = 'var(--green-dark)';
        document.getElementById('upload-proof').style.background = 'rgba(45,90,39,0.03)';
    }
});
</script>
@endsection
