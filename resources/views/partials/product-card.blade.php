{{-- Redesigned Product Card Partial --}}
@php
    $productId    = $product->id;
    $productImage = $product->url_gambar ?? 'images/tent-expedition.png';
    $productName  = $product->nama_produk;
    $productDesc  = $product->deskripsi;
    $productPrice = 'Rp ' . number_format($product->harga_sewa, 0, ',', '.');
    $categoryName = $product->category->nama_kategori ?? 'Alat';

    // Decode spesifikasi_teknis JSON
    $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
    
    // Extract specifications dynamically
    $kapasitas = $specs['kapasitas'] ?? null;
    $berat     = $specs['berat'] ?? null;
    $material  = $specs['material'] ?? null;
    $fitur     = $specs['fitur'] ?? null;
    $comfort   = $specs['comfort'] ?? null;
    $lumen     = $specs['lumen'] ?? null;
    $charging  = $specs['charging'] ?? null;
@endphp

<div class="katalog-card" id="katalog-item-{{ $productId }}">
    <div class="katalog-card-image">
        <span class="katalog-badge">{{ $categoryName }}</span>
        <img src="{{ asset($productImage) }}" alt="{{ $productName }}">
    </div>
    <div class="katalog-card-info">
        <div class="katalog-card-title-row">
            <h3 class="product-name">{{ $productName }}</h3>
            <div class="katalog-rating">
                <i class="fas fa-star"></i> 
                <span>{{ $product->averageRating() > 0 ? number_format($product->averageRating(), 1) : '4.8' }}</span>
                <span class="rating-count">({{ $product->reviewCount() > 0 ? $product->reviewCount() : '16' }})</span>
            </div>
        </div>
        <p class="katalog-card-desc">{{ Str::limit($productDesc, 72) }}</p>
        
        {{-- Minimalist Info Icons based on Category --}}
        <div class="katalog-card-specs">
            @if($kapasitas)
                <span class="spec-icon-badge" title="Kapasitas">
                    <i class="far fa-user"></i> {{ $kapasitas }}
                </span>
            @endif
            @if($berat)
                <span class="spec-icon-badge" title="Berat">
                    <i class="fas fa-weight-hanging"></i> {{ $berat }}
                </span>
            @endif
            @if($material && !$kapasitas)
                <span class="spec-icon-tag">{{ $material }}</span>
            @endif
            @if($fitur && !$kapasitas)
                <span class="spec-icon-tag">{{ $fitur }}</span>
            @endif
            @if($comfort)
                <span class="spec-icon-badge"><i class="fas fa-temperature-half"></i> {{ $comfort }}</span>
            @endif
            @if($lumen)
                <span class="spec-icon-badge"><i class="fas fa-lightbulb"></i> {{ $lumen }}</span>
            @endif
        </div>

        <div class="katalog-card-footer">
            <div class="katalog-price-info">
                <span class="katalog-price">{{ $productPrice }}<span class="price-period">/hari</span></span>
            </div>
            
            <div class="katalog-card-actions">
                <a href="{{ route('produk.detail', $productId) }}" class="btn-card-detail">Detail</a>
                <form action="{{ route('cart.store', $productId) }}" method="POST" style="margin:0;">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <input type="hidden" name="days" value="1">
                    <button type="submit" class="btn-card-cart">+ Keranjang</button>
                </form>
            </div>
        </div>
    </div>
</div>
