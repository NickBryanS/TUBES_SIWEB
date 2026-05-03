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
            {{-- Product cards using partial --}}
            @php
            $products = [
                ['id' => 1, 'image' => 'images/tent-expedition.png', 'name' => 'Apex Summit 4P', 'rating' => '4.9', 'desc' => 'Tenda 4 musim dengan teknologi sirkulasi udara...', 'tags' => ['4 Orang', 'Double Layer', '3.8kg'], 'price' => 'Rp 125.000', 'badge' => 'PROFESSIONAL GRADE', 'badgeClass' => 'badge-professional'],
                ['id' => 2, 'image' => 'images/backpack-product.png', 'name' => 'Nomad Elite 65L', 'rating' => '5.0', 'desc' => 'Tas carrier ergonomis dengan sistem suspensi udara untuk...', 'tags' => ['65 Liter', 'Rain Cover Inc.'], 'price' => 'Rp 85.000', 'badge' => 'TOP RATED', 'badgeClass' => 'badge-toprated'],
                ['id' => 3, 'image' => 'images/stove-product.png', 'name' => 'JetFire Ultra', 'rating' => '4.8', 'desc' => 'Kompor lipat ultra-ringan dengan efisiensi bahan bakar...', 'tags' => ['Titanium', 'Piezo Igniter'], 'price' => 'Rp 45.000', 'badge' => '', 'badgeClass' => ''],
                ['id' => 4, 'image' => 'images/sleepingbag-product.png', 'name' => 'CloudRest Zero', 'rating' => '4.7', 'desc' => 'Sleeping bag bulu angsa sintetis yang memberikan...', 'tags' => ['DRC Comfort', 'Ripstop Nylon'], 'price' => 'Rp 65.000', 'badge' => '', 'badgeClass' => ''],
                ['id' => 5, 'image' => 'images/carrier-product.png', 'name' => 'AeroCore Pro', 'rating' => '4.6', 'desc' => 'Matras angin otomatis dengan insulasi thermal tinggi...', 'tags' => ['Anti selip/slip', 'R-Value 4.2'], 'price' => 'Rp 40.000', 'badge' => '', 'badgeClass' => ''],
                ['id' => 6, 'image' => 'images/headlamp-product.png', 'name' => 'Lumina Beacon', 'rating' => '4.9', 'desc' => 'Lampu kemah multi fungsi dengan baterai tahan lama...', 'tags' => ['1000 Lumens', 'USB-C'], 'price' => 'Rp 30.000', 'badge' => '', 'badgeClass' => ''],
            ];
            @endphp

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
