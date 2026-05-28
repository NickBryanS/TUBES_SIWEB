@extends('layouts.app')

@section('title', 'Katalog Peralatan - Gardakala Outdoor')
@section('description', 'Katalog lengkap peralatan outdoor premium untuk disewa.')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/katalog.css') }}">
@endsection

@section('content')
<div class="katalog-page">
    {{-- MAIN CATALOG CONTENT (full width, no sidebar) --}}
    <div class="katalog-main">
        <div class="katalog-header">
            <div>
                <h1 class="katalog-title">Katalog Peralatan</h1>
                <p class="katalog-subtitle" id="katalog-count-label">Menampilkan {{ $products->count() }} peralatan premium</p>
            </div>
            <div class="katalog-filter-row">
                <div class="filter-dropdown-group">
                    <label class="filter-dropdown-label">Kategori:</label>
                    <select class="filter-dropdown-select" id="filter-category" onchange="applyFilters()">
                        <option value="all">Semua Kategori</option>
                        @php $categories = \App\Models\Category::all(); @endphp
                        @foreach($categories as $cat)
                            <option value="{{ $cat->nama_kategori }}">{{ $cat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="padding: 12px 20px; background: #FAFDFB; border: 1px solid var(--border); border-left: 4px solid var(--green-olive); color: var(--green-dark); border-radius: 8px; margin-bottom: 24px;">
                {{ session('success') }}
            </div>
        @endif

        <div class="katalog-grid" id="katalog-grid">
            @foreach($products as $product)
                @php
                    $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
                    $kapasitasVal = null;
                    if (isset($specs['kapasitas'])) {
                        $kapasitasVal = (int) filter_var($specs['kapasitas'], FILTER_SANITIZE_NUMBER_INT);
                    }
                    $beratVal = null;
                    if (isset($specs['berat'])) {
                        $beratVal = (float) filter_var($specs['berat'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    }
                @endphp
                <div class="katalog-card-container" 
                     data-id="{{ $product->id }}"
                     data-name="{{ strtolower($product->nama_produk) }}"
                     data-category="{{ $product->category->nama_kategori ?? 'Outdoor' }}"
                     data-price="{{ $product->harga_sewa }}"
                     data-kapasitas="{{ $kapasitasVal }}"
                     data-berat="{{ $beratVal }}"
                     data-stok="{{ $product->stok_tersedia }}">
                    @include('partials.product-card', ['product' => $product])
                </div>
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function applyFilters() {
        const selectedCategory = document.getElementById('filter-category').value;
        let visibleCount = 0;

        document.querySelectorAll('.katalog-card-container').forEach(card => {
            const cardCategory = card.getAttribute('data-category');
            const matchesCategory = (selectedCategory === 'all') || (cardCategory === selectedCategory);

            if (matchesCategory) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        document.getElementById('katalog-count-label').innerText = `Menampilkan ${visibleCount} peralatan premium`;
    }

    // Initialize on page load — handle URL query for category pre-select
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const catQuery = urlParams.get('category');
        if (catQuery) {
            const categoryMap = {
                'tenda': 'Tenda',
                'tas-carrier': 'Tas/Carrier',
                'alat-masak': 'Alat Masak',
                'alat-tidur': 'Alat Tidur',
                'aksesoris': 'Aksesoris'
            };
            const mappedName = categoryMap[catQuery] || '';
            if (mappedName) {
                document.getElementById('filter-category').value = mappedName;
                applyFilters();
            }
        }
    });
</script>
@endsection
