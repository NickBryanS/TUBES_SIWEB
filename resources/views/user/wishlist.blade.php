@extends('layouts.app')

@section('title', 'Wishlist - Gardakala Outdoor')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
<style>
    .wishlist-header {
        padding: 40px 20px 20px;
        text-align: center;
    }
    .wishlist-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px 40px;
    }
    .empty-wishlist {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px dashed #ced4da;
        margin-top: 20px;
    }
    .empty-wishlist i {
        font-size: 48px;
        color: #adb5bd;
        margin-bottom: 20px;
    }
    .empty-wishlist p {
        color: #6c757d;
        font-size: 1.1rem;
    }
    .btn-remove-wishlist {
        background: none;
        border: none;
        color: #e63946;
        cursor: pointer;
        padding: 5px;
        font-size: 1.2rem;
        transition: color 0.3s;
    }
    .btn-remove-wishlist:hover {
        color: #c1121f;
    }
</style>
@endsection

@section('content')
<div class="wishlist-page">
    <div class="wishlist-header">
        <h1>Daftar Keinginan (Wishlist)</h1>
        <p>Barang-barang favorit yang siap menemani petualangan Anda.</p>
    </div>

    <div class="wishlist-container">
        @if(session('success'))
            <div class="alert alert-success" style="padding:15px; background:#d4edda; color:#155724; margin-bottom:20px; border-radius:8px; text-align:center;">
                {{ session('success') }}
            </div>
        @endif

        @if($wishlists->isEmpty())
            <div class="empty-wishlist">
                <i class="far fa-heart"></i>
                <p>Wishlist Anda masih kosong. Yuk, cari perlengkapan impian Anda di Katalog!</p>
                <a href="/katalog" class="checkout-btn" style="display:inline-block; margin-top:15px; text-decoration:none; padding:10px 20px; background:#2b2d42; color:white; border-radius:5px;">
                    Eksplorasi Katalog
                </a>
            </div>
        @else
            <div class="katalog-grid">
                @foreach($wishlists as $wish)
                    <div class="katalog-card" style="position: relative;">
                        <!-- Remove from wishlist button -->
                        <form action="{{ route('wishlist.toggle', $wish->product->id) }}" method="POST" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                            @csrf
                            <button type="submit" class="btn-remove-wishlist" title="Hapus dari wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </form>
                        
                        <a href="{{ route('produk.detail', $wish->product->id) }}" style="text-decoration: none; color: inherit;">
                            <div class="katalog-card-image">
                                <img src="{{ $wish->product->url_gambar ?? asset('images/default.png') }}" alt="{{ $wish->product->nama_produk }}">
                            </div>
                            <div class="katalog-card-info">
                                <div class="katalog-card-title-row">
                                    <h3>{{ $wish->product->nama_produk }}</h3>
                                    <div class="katalog-rating"><i class="fas fa-star"></i> 4.5</div>
                                </div>
                                <p class="katalog-card-desc">{{ Str::limit($wish->product->deskripsi, 50) }}</p>
                                <div class="katalog-card-footer">
                                    <div class="katalog-price-info">
                                        <span class="katalog-price-label">SEWA PER HARI</span>
                                        <span class="katalog-price">Rp {{ number_format($wish->product->harga_sewa, 0, ',', '.') }}</span>
                                    </div>
                                    <form action="{{ route('cart.store', $wish->product->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="days" value="1">
                                        <button type="submit" class="katalog-cart-btn" style="position: relative; z-index: 10;"><i class="fas fa-shopping-cart"></i></button>
                                    </form>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
