@extends('layouts.app')

@section('title', 'Daftar Keinginan - Gardakala Outdoor')
@section('nav-wishlist', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
<style>
    .wishlist-page-wrapper {
        padding: 32px 40px;
        max-width: 1400px;
        margin: 0 auto;
        width: 100%;
    }
    .wishlist-header-block {
        margin-bottom: 32px;
    }
    .wishlist-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-dark);
        letter-spacing: -0.02em;
    }
    .wishlist-subtitle {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-top: 4px;
    }
    .wishlist-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .wishlist-card-container {
        position: relative;
        display: flex;
        flex-direction: column;
    }
    .btn-remove-wishlist-top {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 10;
        width: 36px;
        height: 36px;
        background: var(--white);
        border: 1px solid var(--border);
        border-radius: 50%;
        color: var(--red-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        box-shadow: var(--shadow-sm);
        cursor: pointer;
        transition: var(--transition);
    }
    .btn-remove-wishlist-top:hover {
        background: #FFF0F0;
        transform: scale(1.05);
        border-color: var(--red-soft);
    }
    .empty-wishlist-container {
        text-align: center;
        padding: 64px 32px;
        background: var(--white);
        border-radius: var(--radius);
        border: 1px dashed var(--border);
        max-width: 600px;
        margin: 40px auto 0;
        box-shadow: var(--shadow-sm);
    }
    .empty-wishlist-icon {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #FFF0F0;
        color: var(--red-soft);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 1.5rem;
    }
    .empty-wishlist-container h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }
    .empty-wishlist-container p {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-bottom: 24px;
        line-height: 1.5;
    }
    .btn-wishlist-explore {
        display: inline-block;
        padding: 12px 28px;
        background: var(--green-dark);
        color: var(--white);
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: var(--radius-sm);
        transition: var(--transition);
    }
    .btn-wishlist-explore:hover {
        background: var(--green-darker);
    }
    @media (max-width: 992px) {
        .wishlist-grid { grid-template-columns: repeat(2, 1fr); }
        .wishlist-page-wrapper { padding: 16px; }
    }
    @media (max-width: 768px) {
        .wishlist-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="wishlist-page-wrapper">
    <div class="wishlist-header-block">
        <h1 class="wishlist-title">Wishlist Saya</h1>
        <p class="wishlist-subtitle">Daftar perlengkapan outdoor impian yang Anda simpan untuk petualangan selanjutnya.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="padding: 12px 20px; background: #EAFDF0; border: 1px solid var(--border); border-left: 4px solid var(--green-tag); color: var(--green-dark); border-radius: 8px; margin-bottom: 24px;">
            {{ session('success') }}
        </div>
    @endif

    @if($wishlists->isEmpty())
        <div class="empty-wishlist-container">
            <div class="empty-wishlist-icon">
                <i class="fas fa-heart"></i>
            </div>
            <h3>Wishlist Anda Kosong</h3>
            <p>Anda belum menyimpan barang apa pun. Jelajahi katalog lengkap kami untuk menemukan perlengkapan outdoor yang Anda inginkan.</p>
            <a href="/katalog" class="btn-wishlist-explore">Eksplorasi Katalog</a>
        </div>
    @else
        <div class="wishlist-grid">
            @foreach($wishlists as $wish)
                @if($wish->product)
                    <div class="wishlist-card-container">
                        {{-- Absolute Quick Remove Button --}}
                        <form action="{{ route('wishlist.toggle', $wish->product->id) }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-remove-wishlist-top" title="Hapus dari Wishlist">
                                <i class="fas fa-heart"></i>
                            </button>
                        </form>
                        
                        {{-- Include our upgraded Product Card --}}
                        @include('partials.product-card', ['product' => $wish->product])
                    </div>
                @endif
            @endforeach
        </div>
    @endif
</div>
@endsection
