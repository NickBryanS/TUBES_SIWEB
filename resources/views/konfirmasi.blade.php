@extends('layouts.app')

@section('title', 'Konfirmasi Pesanan - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/konfirmasi.css') }}">
@endsection

@section('content')
<div class="konfirmasi-page">
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
                    <span class="order-card-ref">GK-2024-8891-AD <button class="copy-sm"><i class="far fa-copy"></i></button></span>
                </div>
                <div>
                    <span class="order-card-label">STATUS PESANAN</span>
                    <span class="order-status-badge"><i class="fas fa-check-circle"></i> Terkonfirmasi</span>
                </div>
            </div>

            <h3 class="order-card-section-title">Daftar Item</h3>

            <div class="order-item-row">
                <div class="order-item-img">
                    <img src="{{ asset('images/stove-product.png') }}" alt="Apex Carbon Trekking Poles">
                </div>
                <div class="order-item-info">
                    <h4>Apex Carbon Trekking Poles</h4>
                    <p>Sewa: 3 Hari (10 - 13 Nov)</p>
                </div>
                <div class="order-item-price-col">
                    <span class="order-item-price">Rp 450.000</span>
                    <span class="order-item-qty">Qty: 1</span>
                </div>
            </div>

            <div class="order-item-row">
                <div class="order-item-img">
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="Summit Shield 4-Person Tent">
                </div>
                <div class="order-item-info">
                    <h4>Summit Shield 4-Person Tent</h4>
                    <p>Sewa: 3 Hari (10 - 13 Nov)</p>
                </div>
                <div class="order-item-price-col">
                    <span class="order-item-price">Rp 1.200.000</span>
                    <span class="order-item-qty">Qty: 1</span>
                </div>
            </div>

            <div class="order-totals">
                <div class="order-total-row">
                    <span>Subtotal</span>
                    <span>Rp 1.650.000</span>
                </div>
                <div class="order-total-row">
                    <span>Biaya Layanan</span>
                    <span>Rp 25.000</span>
                </div>
                <div class="order-total-row total-final">
                    <span>Total Pembayaran</span>
                    <span>Rp 1.675.000</span>
                </div>
            </div>

            <div class="order-actions">
                <a href="/riwayat" class="btn-track-order">
                    <i class="fas fa-search"></i> Lacak Pesanan Saya
                </a>
                <a href="#" class="btn-download-order">
                    <i class="fas fa-download"></i> Unduh Konfirmasi Pemesanan
                </a>
            </div>

            <p class="order-help">
                Butuh bantuan? <a href="#">Hubungi Layanan Pelanggan</a> atau visit <a href="#">Pusat Bantuan Kami</a>.
            </p>
        </div>
    </div>
</div>
@endsection
