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
            <input type="hidden" name="metode_pembayaran" id="metode_pembayaran" value="qris">
        </form>

        <div class="checkout-grid">
            <!-- LEFT: PAYMENT OPTIONS -->
            <div class="checkout-left">
                <h3 class="checkout-section-title">Metode Pembayaran</h3>

                <!-- Payment Tabs -->
                <div class="payment-tabs" id="payment-tabs">
                    <button class="payment-tab active" data-tab="midtrans" data-value="qris">
                        <i class="fas fa-credit-card"></i> Bayar Online
                    </button>
                    @if(isset($checkoutData['metode_pengambilan']) && $checkoutData['metode_pengambilan'] === 'pickup')
                    <button class="payment-tab" data-tab="cod" data-value="bayar_di_toko">
                        <i class="fas fa-store"></i> Bayar di Toko
                    </button>
                    @endif
                </div>

                <!-- Midtrans (QRIS + VA + E-Wallet) Content -->
                <div class="payment-content" id="tab-midtrans">
                    <div style="text-align: center; padding: 20px 0 8px;">
                        <div style="display: flex; justify-content: center; gap: 14px; flex-wrap: wrap; margin-bottom: 18px;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <div style="width: 56px; height: 56px; background: #f0f7f0; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-qrcode" style="font-size: 1.6rem; color: #2d5a27;"></i>
                                </div>
                                <span style="font-size: 0.75rem; color: #666; font-weight: 500;">QRIS</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <div style="width: 56px; height: 56px; background: #f0f7f0; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-university" style="font-size: 1.6rem; color: #2d5a27;"></i>
                                </div>
                                <span style="font-size: 0.75rem; color: #666; font-weight: 500;">Virtual Account</span>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 6px;">
                                <div style="width: 56px; height: 56px; background: #f0f7f0; border-radius: 14px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-wallet" style="font-size: 1.6rem; color: #2d5a27;"></i>
                                </div>
                                <span style="font-size: 0.75rem; color: #666; font-weight: 500;">E-Wallet</span>
                            </div>
                        </div>
                        <p style="color: #555; font-size: 0.9rem; margin: 0 0 6px;">
                            Pilih metode pembayaranmu setelah mengklik <strong>"Buat Pesanan"</strong>.
                        </p>
                        <p style="color: #888; font-size: 0.8rem; margin: 0;">
                            Mendukung <strong>QRIS</strong>, <strong>Transfer Bank (Virtual Account)</strong>, dan <strong>E-Wallet</strong>.<br>
                            Pembayaran terverifikasi otomatis — tanpa perlu kirim bukti manual.
                        </p>
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
                    @if(isset($ongkosKirim) && $ongkosKirim > 0)
                    <div class="summary-line">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{ number_format($ongkosKirim, 0, ',', '.') }}</span>
                    </div>
                    @endif
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
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
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

// Copy nomor rekening (dinamis)
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text.replace(/[^0-9a-zA-Z]/g, ''));
    var originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check"></i> Disalin!';
    setTimeout(function() {
        btn.innerHTML = originalHTML;
    }, 2000);
}

// File upload preview
document.getElementById('bukti-file')?.addEventListener('change', function() {
    var textEl = document.getElementById('upload-text');
    if (this.files.length > 0) {
        textEl.innerHTML = '<strong><i class="fas fa-check-circle" style="color:var(--green-dark)"></i> ' + this.files[0].name + '</strong>';
        document.getElementById('upload-proof').style.borderColor = 'var(--green-dark)';
        document.getElementById('upload-proof').style.background = 'rgba(45,90,39,0.03)';
    }
});

// Validation on submit / AJAX for QRIS
document.getElementById('pembayaran-form').addEventListener('submit', function(e) {
    let method = document.getElementById('metode_pembayaran').value;
    if (method === 'transfer_bank') {
        let bukti = document.getElementById('bukti-file').files.length;
        if (bukti === 0) {
            e.preventDefault();
            alert('Peringatan: Anda harus mengupload bukti transfer pembayaran sebelum membuat pesanan.');
            return false;
        }
    } else if (method === 'qris') {
        e.preventDefault();
        
        const btn = document.getElementById('btn-create-order');
        const originalText = btn.innerHTML;
        
        // Disable button & show spinner
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        
        const formData = new FormData(this);
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.snap_token) {
                // Tampilkan popup Midtrans Snap
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        window.location.href = data.redirect_url;
                    },
                    onPending: function(result) {
                        window.location.href = data.redirect_url;
                    },
                    onError: function(result) {
                        alert("Pembayaran gagal! Silakan coba lagi.");
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    },
                    onClose: function() {
                        // Jika popup ditutup, tetap redirect ke halaman konfirmasi agar user bisa bayar nanti
                        window.location.href = data.redirect_url;
                    }
                });
            } else {
                alert("Gagal membuat token pembayaran. Silakan coba lagi.");
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Terjadi kesalahan sistem saat memproses pembayaran. Silakan coba lagi.");
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
});
</script>
@endsection
