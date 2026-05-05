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

            @if(session('success'))
                <div class="alert alert-success" style="padding:15px; background:#d4edda; color:#155724; margin-bottom:20px; border-radius:8px;">
                    {{ session('success') }}
                </div>
            @endif

            @php $totalPrice = 0; @endphp

            @forelse($carts as $cart)
                @php $totalPrice += $cart->product->harga_sewa * $cart->quantity; @endphp
                <!-- Item {{ $cart->id }} -->
                <div class="cart-item" id="cart-item-{{ $cart->id }}">
                    <div class="cart-item-image">
                        <img src="{{ $cart->product->url_gambar ?? asset('images/default.png') }}" alt="{{ $cart->product->nama_produk }}" style="width: 100%; height: auto; border-radius: 8px;">
                    </div>
                    <div class="cart-item-details">
                        <div class="cart-item-header">
                            <div>
                                <h3>{{ $cart->product->nama_produk }}</h3>
                                <p class="cart-item-variant">{{ $cart->product->category->nama_kategori ?? 'Umum' }}</p>
                            </div>
                            <form action="{{ route('cart.destroy', $cart->id) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="cart-delete-btn"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                        <div class="cart-item-bottom">
                            <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="cart-qty" style="display: flex; align-items: center; gap: 10px; margin:0;">
                                @csrf
                                @method('PUT')
                                <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stok_tersedia }}" style="width: 60px; padding: 5px; text-align: center; border: 1px solid #ddd; border-radius: 4px;">
                                <button type="submit" style="background: none; border: none; color: #4361ee; cursor: pointer; font-size: 14px; font-weight: 600;"><i class="fas fa-sync-alt"></i> Update</button>
                            </form>
                            <div class="cart-item-price" style="text-align: right;">
                                <span class="price-current" style="font-weight: bold; font-size: 1.1rem; color: #2b2d42;">Rp {{ number_format($cart->product->harga_sewa * $cart->quantity, 0, ',', '.') }}</span><br>
                                <span class="price-period" style="font-size: 0.85rem; color: #8d99ae;">({{ $cart->quantity }} x Rp {{ number_format($cart->product->harga_sewa, 0, ',', '.') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="text-align: center; padding: 40px; background: #f8f9fa; border-radius: 12px; border: 1px dashed #ced4da;">
                    <i class="fas fa-shopping-cart" style="font-size: 48px; color: #adb5bd; margin-bottom: 20px;"></i>
                    <p style="color: #6c757d; font-size: 1.1rem;">Keranjang belanja Anda masih kosong.</p>
                </div>
            @endforelse

            <a href="/katalog" class="add-more-btn" style="display:inline-block; text-align:center; text-decoration:none; margin-top: 20px;">
                <i class="fas fa-plus-circle"></i> Tambah Peralatan Lain
            </a>
        </div>

        <!-- RIGHT: SUMMARY -->
        <div class="order-summary" id="order-summary">
            <h3 class="summary-title">Ringkasan Pesanan</h3>
            <div class="summary-rows">
                <div class="summary-row">
                    <span>Sewa Alat ({{ $carts->sum('quantity') }} item)</span>
                    <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                @php
                    $pajak = $totalPrice * 0.11;
                    $totalAkhir = $totalPrice + $pajak;
                @endphp
                <div class="summary-row">
                    <span>Pajak (11%)</span>
                    <span>Rp {{ number_format($pajak, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="summary-total">
                <span>Total Biaya Sewa</span>
                <span class="total-price">Rp {{ number_format($totalAkhir, 0, ',', '.') }}</span>
            </div>
            <a href="{{ route('checkout') }}" class="checkout-btn" id="checkout-btn" @if($carts->isEmpty()) style="pointer-events: none; opacity: 0.5;" @endif>
                Lanjut ke Pembayaran <i class="fas fa-arrow-right"></i>
            </a>
            <p class="checkout-note">
                <i class="fas fa-info-circle"></i>
                Stok terbatas, ketersediaan diproses saat checkout.
            </p>

            <div class="assurance-box">
                <div class="assurance-icon"><i class="fas fa-shield-alt"></i></div>
                <div>
                    <strong>Gardakala Assurance</strong>
                    <p>Peralatan kami telah melalui proses sterilisasi dan pengecekan ketat.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
