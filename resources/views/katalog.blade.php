@extends('layouts.app')

@section('title', 'Katalog Peralatan - Gardakala Outdoor')
@section('description', 'Katalog lengkap peralatan outdoor premium untuk disewa.')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
@endsection

@section('content')
<div class="katalog-page">
    <div class="katalog-main">
        <div class="katalog-header">
            <div>
                <h1 class="katalog-title">Katalog Peralatan</h1>
                <p class="katalog-subtitle">Temukan perlengkapan camping dan mendaki terbaik untuk petualangan Anda.</p>
            </div>
            <div style="display: flex; align-items: flex-end; font-size: 0.85rem; color: var(--text-light); margin-bottom: 4px;">
                Menampilkan <span id="product-count" style="font-weight: 700; color: var(--green-dark); margin: 0 4px;">{{ $products->count() }}</span> produk
            </div>
        </div>

        {{-- FILTER BAR --}}
        <div class="katalog-filters">
            <div class="filter-search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="filter-search" placeholder="Cari nama produk..." autocomplete="off">
            </div>
            <div class="filter-selects">
                <div class="filter-select-wrapper">
                    <i class="fas fa-tags filter-select-icon"></i>
                    <select id="filter-kategori">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-select-wrapper">
                    <i class="fas fa-sort-amount-down filter-select-icon"></i>
                    <select id="filter-harga">
                        <option value="">Urutkan Harga</option>
                        <option value="low">Harga Terendah</option>
                        <option value="high">Harga Tertinggi</option>
                    </select>
                </div>
                <div class="filter-select-wrapper">
                    <i class="fas fa-users filter-select-icon"></i>
                    <select id="filter-kapasitas">
                        <option value="">Semua Kapasitas</option>
                        @php
                            $kapasitasList = $products->map(function($p) {
                                $specs = json_decode($p->spesifikasi_teknis ?? '{}', true) ?: [];
                                return $specs['kapasitas'] ?? null;
                            })->filter()->unique()->sort()->values();
                        @endphp
                        @foreach($kapasitasList as $kap)
                            <option value="{{ $kap }}">{{ $kap }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <button class="filter-reset-btn" id="filter-reset" style="display:none;">
                <i class="fas fa-times"></i> Reset
            </button>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="padding: 12px 20px; background: #FAFDFB; border: 1px solid var(--border); border-left: 4px solid var(--green-primary); color: var(--green-dark); border-radius: 8px; margin-bottom: 24px;">
                {{ session('success') }}
            </div>
        @endif

        {{-- EMPTY STATE (hidden by default) --}}
        <div class="katalog-empty-state" id="katalog-empty" style="display:none;">
            <i class="fas fa-search"></i>
            <p>Tidak ada produk yang cocok dengan filter Anda.</p>
            <button class="filter-reset-btn" onclick="document.getElementById('filter-reset').click()">
                <i class="fas fa-undo"></i> Reset Filter
            </button>
        </div>

        <div class="katalog-grid" id="katalog-grid">
            @foreach($products as $product)
                @php
                    $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
                @endphp
                <div class="katalog-card-container"
                     data-name="{{ strtolower($product->nama_produk) }}"
                     data-category="{{ $product->category->nama_kategori ?? '' }}"
                     data-price="{{ $product->harga_sewa }}"
                     data-kapasitas="{{ $specs['kapasitas'] ?? '' }}">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput   = document.getElementById('filter-search');
    const kategoriSel   = document.getElementById('filter-kategori');
    const hargaSel      = document.getElementById('filter-harga');
    const kapasitasSel  = document.getElementById('filter-kapasitas');
    const resetBtn      = document.getElementById('filter-reset');
    const grid          = document.getElementById('katalog-grid');
    const countEl       = document.getElementById('product-count');
    const emptyState    = document.getElementById('katalog-empty');
    const cards         = Array.from(grid.querySelectorAll('.katalog-card-container'));

    function applyFilters() {
        const search    = searchInput.value.toLowerCase().trim();
        const kategori  = kategoriSel.value;
        const kapasitas = kapasitasSel.value;
        const harga     = hargaSel.value;

        // Show/hide reset button
        const hasFilter = search || kategori || kapasitas || harga;
        resetBtn.style.display = hasFilter ? 'inline-flex' : 'none';

        // Filter
        let visibleCards = [];
        cards.forEach(card => {
            const matchName = !search || card.dataset.name.includes(search);
            const matchCat  = !kategori || card.dataset.category === kategori;
            const matchKap  = !kapasitas || card.dataset.kapasitas === kapasitas;

            if (matchName && matchCat && matchKap) {
                card.style.display = '';
                visibleCards.push(card);
            } else {
                card.style.display = 'none';
            }
        });

        // Sort by price
        if (harga) {
            visibleCards.sort((a, b) => {
                const priceA = parseFloat(a.dataset.price);
                const priceB = parseFloat(b.dataset.price);
                return harga === 'low' ? priceA - priceB : priceB - priceA;
            });
            // Re-append sorted cards
            visibleCards.forEach(card => grid.appendChild(card));
            // Also append hidden cards at the end
            cards.filter(c => c.style.display === 'none').forEach(c => grid.appendChild(c));
        }

        // Update count
        countEl.textContent = visibleCards.length;

        // Empty state
        emptyState.style.display = visibleCards.length === 0 ? 'flex' : 'none';
        grid.style.display = visibleCards.length === 0 ? 'none' : '';
    }

    searchInput.addEventListener('input', applyFilters);
    kategoriSel.addEventListener('change', applyFilters);
    hargaSel.addEventListener('change', applyFilters);
    kapasitasSel.addEventListener('change', applyFilters);

    resetBtn.addEventListener('click', function() {
        searchInput.value = '';
        kategoriSel.value = '';
        hargaSel.value = '';
        kapasitasSel.value = '';
        applyFilters();
    });

    // Entrance animation
    document.querySelectorAll('.katalog-filters, .katalog-card-container').forEach((el, i) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(16px)';
        setTimeout(() => {
            el.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, 60 + i * 50);
    });
});
</script>
@endsection
