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
    @include('partials.checkout-stepper', ['currentStep' => 3])

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
                $biayaAdmin = 2500;
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
                <a href="{{ route('pesanan.detail', $transaction->id) }}" class="btn-track-order">
                    <i class="fas fa-search"></i> Lacak Pesanan Saya
                </a>
                <a href="{{ route('riwayat') }}" class="btn-download-order">
                    <i class="fas fa-history"></i> Lihat Riwayat
                </a>
            </div>

            <p class="order-help">
                Butuh bantuan? <a href="#">Hubungi Layanan Pelanggan</a> atau visit <a href="#">Pusat Bantuan Kami</a>.
            </p>
        </div>
    </div>
</div>
@endsection

