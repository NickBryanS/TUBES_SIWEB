@extends('layouts.app')

@section('title', 'Katalog Peralatan - Gardakala Outdoor')
@section('description', 'Katalog lengkap peralatan outdoor untuk disewa di Gardakala Outdoor.')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
@endsection

@section('content')
<div class="katalog-page">
    {{-- SIDEBAR FILTER --}}
    <aside class="filter-sidebar" id="filter-sidebar">
        <h3 class="filter-title">Filter Peralatan</h3>

        <div class="filter-group">
            <h4 class="filter-label">KATEGORI</h4>
            <label class="filter-checkbox">
                <input type="checkbox" checked> <span class="checkmark"></span> Tenda
            </label>
            <label class="filter-checkbox">
                <input type="checkbox"> <span class="checkmark"></span> Alat Masak
            </label>
            <label class="filter-checkbox">
                <input type="checkbox"> <span class="checkmark"></span> Tas / Carrier
            </label>
            <label class="filter-checkbox">
                <input type="checkbox"> <span class="checkmark"></span> Alat Tidur
            </label>
        </div>

        <div class="filter-group">
            <h4 class="filter-label">RENTANG HARGA (RP)</h4>
            <div class="price-range">
                <input type="range" min="0" max="500000" value="300000" class="range-slider" id="price-range">
                <div class="price-labels">
                    <span>25rb</span>
                    <span>500rb</span>
                </div>
            </div>
        </div>

        <div class="filter-group">
            <h4 class="filter-label">HANYA TERSEDIA</h4>
            <label class="toggle-switch">
                <input type="checkbox" checked>
                <span class="toggle-slider"></span>
            </label>
        </div>

        <button class="filter-apply-btn" id="apply-filter">TERAPKAN FILTER</button>
    </aside>

    {{-- MAIN CATALOG CONTENT --}}
    <div class="katalog-main">
        <div class="katalog-header">
            <div>
                <h1 class="katalog-title">Katalog Peralatan</h1>
                <p class="katalog-subtitle">Menampilkan 12 peralatan premium</p>
            </div>
            <div class="katalog-sort">
                <label class="sort-label">URUTKAN</label>
                <select class="sort-select" id="sort-select">
                    <option>Terbaru</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                    <option>Rating Tertinggi</option>
                </select>
            </div>
        </div>

        <div class="katalog-grid" id="katalog-grid">
            {{-- Product cards from database --}}
            @foreach($products as $product)
                @include('partials.product-card', ['product' => $product])
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="katalog-pagination" id="katalog-pagination">
            <button class="page-btn"><i class="fas fa-chevron-left"></i></button>
            <button class="page-btn active">1</button>
            <button class="page-btn">2</button>
            <button class="page-btn">3</button>
            <button class="page-btn"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>
@endsection
