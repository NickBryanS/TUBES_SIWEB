@extends('layouts.app')

@section('title', 'Keranjang Belanja - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/keranjang.css') }}">
@endsection

@section('content')
<div class="keranjang-page">
    <div class="keranjang-container">
        {{-- LEFT PANEL: PRODUCTS LIST --}}
        <div class="keranjang-items-panel">
            <h1 class="keranjang-title">Keranjang Rental</h1>
            <p class="keranjang-subtitle">Siapkan petualanganmu dengan perlengkapan outdoor pilihan terbaik.</p>

            @if(session('success'))
                <div class="success-alert-banner">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @php $totalPrice = 0; @endphp

            @forelse($carts as $cart)
                @php $totalPrice += $cart->product->harga_sewa * $cart->quantity * $cart->days; @endphp
                <div class="cart-item-card" id="cart-item-{{ $cart->id }}">
                    <div class="cart-item-image">
                        <img src="{{ asset($cart->product->url_gambar ?? 'images/tent-expedition.png') }}" alt="{{ $cart->product->nama_produk }}">
                    </div>
                    
                    <div class="cart-item-details">
                        <div class="cart-item-header">
                            <div>
                                <span class="cart-category-tag">{{ $cart->product->category->nama_kategori ?? 'Peralatan' }}</span>
                                <h3 class="cart-item-name">{{ $cart->product->nama_produk }}</h3>
                                <div class="cart-duration-badge">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Durasi Sewa: <strong>{{ $cart->days }} Hari</strong></span>
                                </div>
                            </div>
                            <form action="{{ route('cart.destroy', $cart->id) }}" method="POST" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-cart-delete" title="Hapus dari keranjang">
                                    <i class="far fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>

                        <div class="cart-item-footer">
                            {{-- Qty Updater --}}
                            <form action="{{ route('cart.update', $cart->id) }}" method="POST" class="cart-qty-form">
                                @csrf
                                @method('PUT')
                                <div class="qty-field-wrapper">
                                    <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" max="{{ $cart->product->stok_tersedia }}">
                                    <input type="hidden" name="days" value="{{ $cart->days }}">
                                    <button type="submit" class="btn-qty-update" title="Perbarui jumlah"><i class="fas fa-sync-alt"></i> Update</button>
                                </div>
                            </form>

                            {{-- Price Calc --}}
                            <div class="cart-item-price-block">
                                <span class="price-val">Rp {{ number_format($cart->product->harga_sewa * $cart->quantity * $cart->days, 0, ',', '.') }}</span>
                                <span class="price-breakdown">({{ $cart->quantity }} unit x {{ $cart->days }} hari x Rp {{ number_format($cart->product->harga_sewa, 0, ',', '.') }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-cart-card">
                    <div class="empty-cart-icon">
                        <i class="fas fa-shopping-bag"></i>
                    </div>
                    <h3>Keranjang Rental Kosong</h3>
                    <p>Belum ada perlengkapan yang dimasukkan ke dalam keranjang belanja Anda.</p>
                    <a href="/katalog" class="btn-rent-now">Eksplorasi Katalog</a>
                </div>
            @endforelse

            @if(!$carts->isEmpty())
                <a href="/katalog" class="btn-add-more">
                    <i class="fas fa-plus"></i> Tambah Perlengkapan Lain
                </a>
            @endif
        </div>

        {{-- RIGHT PANEL: SUMMARY BOX --}}
        @if(!$carts->isEmpty())
            <aside class="order-summary-sidebar">
                <h3 class="summary-title">Ringkasan Rental</h3>
                
                <div class="summary-details-block">
                    <div class="summary-detail-row">
                        <span>Biaya Sewa Alat</span>
                        <span>Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="summary-total-block">
                    <span>Total Estimasi</span>
                    <span class="total-price-green">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('checkout') }}" class="btn-checkout-primary">
                    Lanjut ke Pembayaran <i class="fas fa-arrow-right"></i>
                </a>
                
                <p class="summary-checkout-hint">
                    <i class="fas fa-circle-info"></i>
                    Stok terbatas, ketersediaan unit sewa diproses saat checkout berhasil.
                </p>

                {{-- Guarantee Card --}}
                <div class="assurance-sidebar-card">
                    <div class="assurance-icon-circle"><i class="fas fa-shield-halved"></i></div>
                    <div>
                        <h5>Gardakala Assurance</h5>
                        <p>Semua perlengkapan kami melewati proses pemeriksaan kualitas & sterilisasi intensif.</p>
                    </div>
                </div>
            </aside>
        @endif
    </div>
</div>
@endsection
