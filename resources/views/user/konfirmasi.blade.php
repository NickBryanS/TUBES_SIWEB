@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
<link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">
@endsection

@section('content')
<div class="konfirmasi-page">
    <!-- Stepper -->


    <!-- Background Mountain -->
    <div class="konfirmasi-hero">
        <img src="{{ asset('images/hero-mountains.png') }}" alt="Mountains" class="konfirmasi-bg">
        <div class="konfirmasi-bg-overlay"></div>
    </div>

    <div class="konfirmasi-content">
        <!-- Success Icon -->
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>

        <h1 class="konfirmasi-title">Sukses! Pesanan Anda telah dibuat</h1>
        <p class="konfirmasi-subtitle">Terima kasih telah memilih Gardakala. Petualangan luar biasa Anda.</p>

        <!-- Order Card -->
        <div class="order-card" id="order-confirmation-card">
            <div class="order-card-header">
                <div>
                    <span class="order-card-label">NOMOR REFERENSI</span>
                    <span class="order-card-ref">GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }} <button class="copy-sm"><i class="far fa-copy"></i></button></span>
                </div>
                <div>
                    <span class="order-card-label">STATUS PESANAN</span>
                    @if($transaction->status_transaksi === 'menunggu')
                        <span class="order-status-badge" style="background: #fff3e0; color: #e65100;"><i class="fas fa-clock"></i> Menunggu Pembayaran</span>
                    @elseif($transaction->status_transaksi === 'menunggu_admin')
                        <span class="order-status-badge"><i class="fas fa-check-circle"></i> Menunggu Verifikasi</span>
                    @else
                        <span class="order-status-badge"><i class="fas fa-check-circle"></i> Terkonfirmasi</span>
                    @endif
                </div>
            </div>

            <h3 class="order-card-section-title">Daftar Item</h3>

            @php
                $subtotal = 0;
                $durasi = $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai);
            @endphp

            @foreach($transaction->details as $detail)
                @php
                    $itemTotal = $detail->product->harga_sewa * $detail->jumlah * $durasi;
                    $subtotal += $itemTotal;
                @endphp
                <div class="order-item-row">
                    <div class="order-item-img">
                        <img src="{{ asset($detail->product->url_gambar ?? 'images/placeholder.png') }}" alt="{{ $detail->product->nama_produk }}">
                    </div>
                    <div class="order-item-info">
                        <h4>{{ $detail->product->nama_produk }}</h4>
                        <p>Sewa: {{ $durasi }} Hari ({{ $transaction->tanggal_mulai->format('d M') }} - {{ $transaction->tanggal_selesai->format('d M') }})</p>
                    </div>
                    <div class="order-item-price-col">
                        <span class="order-item-price">Rp {{ number_format($itemTotal, 0, ',', '.') }}</span>
                        <span class="order-item-qty">Qty: {{ $detail->jumlah }}</span>
                    </div>
                </div>
            @endforeach

            @php
                $biayaAdmin = 0;
                $total = $transaction->total_biaya;
            @endphp

            <div class="order-totals">
                <div class="order-total-row">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="order-total-row">
                    <span>Biaya Admin</span>
                    <span>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</span>
                </div>
                <div class="order-total-row total-final">
                    <span>Total Pembayaran</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="order-actions">
                @if($transaction->payment->metode_pembayaran === 'bayar_di_toko' && $transaction->metode_pengambilan !== 'deliver')
                    <a href="{{ route('pesanan.nota', $transaction->id) }}" class="btn-track-order" target="_blank">
                        <i class="fas fa-file-invoice"></i> Unduh Nota
                    </a>
                @else
                    <a href="{{ route('pesanan.detail', $transaction->id) }}" class="btn-track-order">
                        <i class="fas fa-search"></i> Lacak Pesanan Saya
                    </a>
                @endif
                <a href="{{ route('riwayat') }}" class="btn-download-order">
                    <i class="fas fa-history"></i> Lihat Riwayat
                </a>
            </div>

            {{-- PEMBAYARAN MIDTRANS QRIS --}}
            @if($transaction->status_transaksi === 'menunggu' && $transaction->payment->metode_pembayaran === 'qris')
            <div style="margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.1); text-align: center;">
                @if(isset($snapToken) && $snapToken)
                    <button id="pay-button"
                            style="width: 100%; padding: 14px; background: linear-gradient(135deg, #2d5a27, #3a7d32); border: none; border-radius: 10px; color: #fff; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                        <i class="fas fa-wallet"></i> Bayar Sekarang
                    </button>
                @else
                    <div style="padding: 12px; background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.3); border-radius: 8px; color: #e74c3c; font-size: 0.9rem;">
                        <i class="fas fa-exclamation-circle"></i> Gagal memuat token pembayaran. Hubungi admin atau silakan muat ulang halaman.
                    </div>
                @endif
            </div>
            {{-- UPLOAD BUKTI PEMBAYARAN TRANSFER BANK --}}
            @elseif($transaction->status_transaksi === 'menunggu' && $transaction->payment->metode_pembayaran === 'transfer_bank')
            <div style="margin-top: 28px; padding-top: 24px; border-top: 1px solid rgba(255,255,255,0.1);">
                <h3 style="color: #fff; font-size: 1.1rem; margin-bottom: 16px;">
                    <i class="fas fa-upload" style="color: #e8a838;"></i> Upload Bukti Pembayaran
                </h3>

                {{-- Form Upload --}}
                <form action="{{ route('pembayaran.upload', $transaction->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div id="upload-area-konfirmasi" onclick="document.getElementById('bukti-file-konfirmasi').click()"
                         style="border: 2px dashed rgba(255,255,255,0.15); border-radius: 12px; padding: 28px; text-align: center; cursor: pointer; transition: all 0.3s; background: rgba(255,255,255,0.02);">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: rgba(255,255,255,0.3); margin-bottom: 8px;"></i>
                        <p id="upload-text-konfirmasi" style="color: rgba(255,255,255,0.6); margin: 0;">
                            <strong>Klik atau seret file ke sini</strong>
                        </p>
                        <span style="color: rgba(255,255,255,0.3); font-size: 0.8rem;">Format: JPG, PNG, PDF (maks. 5MB)</span>
                        <input type="file" name="bukti_pembayaran" id="bukti-file-konfirmasi" accept=".jpg,.jpeg,.png,.pdf" style="display:none;" required>
                    </div>

                    <button type="submit"
                            style="width: 100%; margin-top: 16px; padding: 14px; background: linear-gradient(135deg, #2d5a27, #3a7d32); border: none; border-radius: 10px; color: #fff; font-size: 1rem; font-weight: 600; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;">
                        <i class="fas fa-paper-plane"></i> Kirim Bukti Pembayaran
                    </button>
                </form>

                <div style="display: flex; align-items: flex-start; gap: 8px; margin-top: 14px; color: rgba(255,255,255,0.4); font-size: 0.8rem;">
                    <i class="fas fa-exclamation-circle" style="margin-top: 2px;"></i>
                    <span>Pesanan Anda akan diverifikasi secara manual oleh tim kami dalam waktu maksimal 1x24 jam setelah bukti transfer diunggah.</span>
                </div>
            </div>
            @elseif($transaction->status_transaksi === 'menunggu' && $transaction->payment->metode_pembayaran === 'bayar_di_toko')
            <div style="margin-top: 28px; padding: 20px; background: rgba(232,168,56,0.08); border: 1px solid rgba(232,168,56,0.2); border-radius: 12px; text-align: center;">
                <i class="fas fa-store" style="font-size: 1.5rem; color: #e8a838; margin-bottom: 8px;"></i>
                <p style="color: #e8a838; font-weight: 600; margin: 0;">Pembayaran Langsung di Toko</p>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-top: 4px;">Silakan selesaikan pembayaran tunai saat Anda mengambil peralatan di Basecamp GKDL Outdoor.</p>
            </div>
            @elseif($transaction->status_transaksi === 'menunggu_admin')
            <div style="margin-top: 28px; padding: 20px; background: rgba(46,204,113,0.08); border: 1px solid rgba(46,204,113,0.2); border-radius: 12px; text-align: center;">
                <i class="fas fa-hourglass-half" style="font-size: 1.5rem; color: #2ecc71; margin-bottom: 8px;"></i>
                <p style="color: #2ecc71; font-weight: 600; margin: 0;">Bukti pembayaran sudah diunggah</p>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.85rem; margin-top: 4px;">Menunggu verifikasi dari tim kami. Estimasi 1x24 jam.</p>
            </div>
            @endif

            <p class="order-help">
                Butuh bantuan? <a href="#">Hubungi Layanan Pelanggan</a> atau visit <a href="#">Pusat Bantuan Kami</a>.
            </p>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(isset($snapToken) && $snapToken)
<script src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}" data-client-key="{{ config('midtrans.client_key') }}"></script>
<script>
// Buka popup Snap otomatis saat halaman selesai dimuat (jika belum ada indikasi callback redirect)
@if(!request()->has('transaction_status') && !request()->has('order_id'))
window.addEventListener('DOMContentLoaded', (event) => {
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.reload();
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert("Pembayaran gagal! Silakan coba lagi.");
        },
        onClose: function() {
            // Pengguna menutup popup, biarkan mereka di halaman ini agar bisa klik "Bayar Sekarang" nanti
        }
    });
});
@endif

document.getElementById('pay-button')?.addEventListener('click', function(e) {
    e.preventDefault();
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result) {
            window.location.reload();
        },
        onPending: function(result) {
            window.location.reload();
        },
        onError: function(result) {
            alert("Pembayaran gagal! Silakan coba lagi.");
        },
        onClose: function() {
            alert('Anda menutup popup pembayaran sebelum menyelesaikan transaksi.');
        }
    });
});
</script>
@endif
<script>
document.getElementById('bukti-file-konfirmasi')?.addEventListener('change', function() {
    var textEl = document.getElementById('upload-text-konfirmasi');
    var areaEl = document.getElementById('upload-area-konfirmasi');
    if (this.files.length > 0) {
        textEl.innerHTML = '<strong><i class="fas fa-check-circle" style="color:#2ecc71"></i> ' + this.files[0].name + '</strong>';
        areaEl.style.borderColor = '#2ecc71';
        areaEl.style.background = 'rgba(46,204,113,0.05)';
    }
});
</script>
@endsection
