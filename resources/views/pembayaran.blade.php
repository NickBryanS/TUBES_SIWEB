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

        <div class="checkout-grid">
            <!-- LEFT: PAYMENT OPTIONS -->
            <div class="checkout-left">
                <h3 class="checkout-section-title">Metode Pembayaran</h3>

                <!-- Payment Tabs -->
                <div class="payment-tabs" id="payment-tabs">
                    <button class="payment-tab active" data-tab="transfer">
                        <i class="fas fa-university"></i> Transfer Bank
                    </button>
                    <button class="payment-tab" data-tab="qris">
                        <i class="fas fa-qrcode"></i> QRIS
                    </button>
                    <button class="payment-tab" data-tab="cod">
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
                            <button class="copy-btn" id="copy-bank"><i class="fas fa-copy"></i> Salin Nomor</button>
                        </div>
                    </div>

                    <h4 class="upload-title">Upload Bukti Transfer</h4>
                    <div class="upload-area" id="upload-proof">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Klik atau seret file ke sini</p>
                        <span>Format: JPG, PNG, PDF (maks. 5MB). Pastikan detail transfer terlihat jelas.</span>
                    </div>

                    <div class="payment-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <span>Pesanan Anda akan diverifikasi secara manual oleh tim kami dalam waktu maksimal 1x24 jam setelah bukti transfer diunggah.</span>
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

                <!-- QRIS Content -->
                <div class="payment-content" id="tab-qris" style="display:none;">
                    <div class="qris-info">
                        <div class="qris-placeholder">
                            <i class="fas fa-qrcode"></i>
                            <p>Scan QR code di bawah untuk melakukan pembayaran</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT: ORDER SUMMARY -->
            <div class="checkout-right">
                <div class="checkout-summary">
                    <h3>RINGKASAN PESANAN</h3>
                    <div class="summary-items">
                        <div class="summary-item">
                            <div>
                                <span class="si-name">Tenda Kapasitas 4 Orang</span>
                                <span class="si-detail">3 Unit - 3 Hari</span>
                            </div>
                            <span class="si-price">Rp 640.000</span>
                        </div>
                        <div class="summary-item">
                            <div>
                                <span class="si-name">Sleeping Bag Premium</span>
                                <span class="si-detail">4 Unit - 3 Hari</span>
                            </div>
                            <span class="si-price">Rp 240.000</span>
                        </div>
                    </div>
                    <div class="summary-line">
                        <span>Subtotal</span>
                        <span>Rp 690.000</span>
                    </div>
                    <div class="summary-line">
                        <span>Biaya Admin</span>
                        <span>Rp 2.500</span>
                    </div>
                    <div class="summary-total-line">
                        <span>Total</span>
                        <span class="summary-total-price">Rp 692.500</span>
                    </div>
                    <a href="/konfirmasi" class="btn-proceed btn-create-order" id="btn-create-order">
                        BUAT PESANAN <i class="fas fa-plus"></i>
                    </a>
                </div>

                <!-- Promo Banner -->
                <div class="promo-banner">
                    <img src="{{ asset('images/mountain-adventure.png') }}" alt="Promo">
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
document.querySelectorAll('.payment-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.payment-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        
        document.querySelectorAll('.payment-content').forEach(c => c.style.display = 'none');
        document.getElementById('tab-' + this.dataset.tab).style.display = '';
    });
});

document.getElementById('copy-bank')?.addEventListener('click', function() {
    navigator.clipboard.writeText('123-456-7890');
    this.innerHTML = '<i class="fas fa-check"></i> Disalin!';
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-copy"></i> Salin Nomor';
    }, 2000);
});
</script>
@endsection
