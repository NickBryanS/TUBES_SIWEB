@extends('layouts.app')

@section('title', 'Keranjang Belanja - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/keranjang.css') }}">
@endsection

@section('content')
<div class="keranjang-page">
    <div class="keranjang-container">
        <!-- LEFT: ITEMS -->
        <div class="keranjang-items">
            <h1 class="keranjang-title">Keranjang Belanja</h1>
            <p class="keranjang-subtitle">Siapkan petualangan Anda dengan peralatan pilihan terbaik.</p>

            <!-- Item 1 -->
            <div class="cart-item" id="cart-item-1">
                <div class="cart-item-image">
                    <span class="cart-badge">Professional Grade</span>
                    <img src="{{ asset('images/tent-expedition.png') }}" alt="Tenda Peak Performance 3P">
                    <span class="cart-safe-tag">SAFE CATALYSMT</span>
                </div>
                <div class="cart-item-details">
                    <div class="cart-item-header">
                        <div>
                            <h3>Tenda Peak Performance 3P</h3>
                            <p class="cart-item-variant">UltraPro 6-SeM 6, 2Pcs</p>
                            <p class="cart-item-date"><i class="fas fa-calendar"></i> 12 - 15 Okt 2024 (3 Hari)</p>
                        </div>
                        <button class="cart-delete-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="cart-item-bottom">
                        <div class="cart-qty">
                            <button class="qty-btn"><i class="fas fa-minus"></i></button>
                            <span>1</span>
                            <button class="qty-btn"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="cart-item-price">
                            <span class="price-original">Rp 450.000</span>
                            <span class="price-current">Rp 375.000</span>
                            <span class="price-period">Per periode</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item 2 -->
            <div class="cart-item" id="cart-item-2">
                <div class="cart-item-image">
                    <span class="cart-badge badge-seller">Best Seller</span>
                    <img src="{{ asset('images/backpack-product.png') }}" alt="Osprey Aether 65L">
                </div>
                <div class="cart-item-details">
                    <div class="cart-item-header">
                        <div>
                            <h3>Osprey Aether 65L</h3>
                            <p class="cart-item-variant">Anti-Gravity SusCen 6, RPstr der ticketed</p>
                            <p class="cart-item-date"><i class="fas fa-calendar"></i> 13 - 15 Okt 2024 (3 Hari)</p>
                        </div>
                        <button class="cart-delete-btn"><i class="fas fa-trash"></i></button>
                    </div>
                    <div class="cart-item-bottom">
                        <div class="cart-qty">
                            <button class="qty-btn"><i class="fas fa-minus"></i></button>
                            <span>2</span>
                            <button class="qty-btn"><i class="fas fa-plus"></i></button>
                        </div>
                        <div class="cart-item-price">
                            <span class="price-current">Rp 520.000</span>
                            <span class="price-period">Per periode (2 unit)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add More -->
            <button class="add-more-btn" id="add-more-items">
                <i class="fas fa-plus-circle"></i> Tambah Peralatan Lain
            </button>
        </div>

        <!-- RIGHT: SUMMARY -->
        <div class="order-summary" id="order-summary">
            <h3 class="summary-title">Ringkasan Pesanan</h3>
            <div class="summary-rows">
                <div class="summary-row">
                    <span>Sewa Alat (1 item)</span>
                    <span>Rp 895.000</span>
                </div>
                <div class="summary-row">
                    <span>BiaYal SRT P (3 HRI)</span>
                    <span>Rp 184.300</span>
                </div>
                <div class="summary-row">
                    <span>BIAY UiVRPS</span>
                    <span>Rp 203.200</span>
                </div>
                <div class="summary-row">
                    <span>PPR (11%)</span>
                    <span>Rp 117.700</span>
                </div>
            </div>
            <div class="summary-total">
                <span>Total Biaya Sewa</span>
                <span class="total-price">Rp 1.187.700</span>
            </div>
            <a href="/checkout" class="checkout-btn" id="checkout-btn">
                Lanjut ke Pembayaran <i class="fas fa-arrow-right"></i>
            </a>
            <p class="checkout-note">
                <i class="fas fa-info-circle"></i>
                Stok terbatas, harga dan ketersediaan dan penawaran langsung oleh vendor terpercaya.
            </p>

            <div class="assurance-box">
                <div class="assurance-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <strong>Gardakala Assurance</strong>
                    <p>Peralatan kami telah melalui proses sterilisasi dan pengecekan katro selengap mungkin.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
