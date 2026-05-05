{{-- Partials: Product Card (Katalog)
    @param $product - Eloquent Product model
--}}
@php
    $productId    = $product->id;
    $productImage = $product->url_gambar ?? 'images/tent-expedition.png';
    $productName  = $product->nama_produk;
    $productDesc  = $product->deskripsi;
    $productPrice = 'Rp ' . number_format($product->harga_sewa, 0, ',', '.');
    $categoryName = $product->category->nama_kategori ?? '';

    // Decode spesifikasi_teknis JSON untuk tags
    $specs = json_decode($product->spesifikasi_teknis ?? '{}', true) ?: [];
    $tags  = array_values($specs);

    // Badge berdasarkan kategori
    $badge      = $categoryName ? strtoupper($categoryName) : '';
    $badgeClass = $categoryName ? 'badge-professional' : '';
@endphp

<a href="{{ route('produk.detail', $productId) }}" class="katalog-card" id="katalog-item-{{ $productId }}">
    <div class="katalog-card-image">
        @if(!empty($badge))
            <span class="katalog-badge {{ $badgeClass }}">{{ $badge }}</span>
        @endif
        <img src="{{ asset($productImage) }}" alt="{{ $productName }}">
    </div>
    <div class="katalog-card-info">
        <div class="katalog-card-title-row">
            <h3>{{ $productName }}</h3>
            <div class="katalog-rating"><i class="fas fa-star"></i> 4.9</div>
        </div>
        <p class="katalog-card-desc">{{ $productDesc }}</p>
        <div class="katalog-card-tags">
            @foreach($tags as $tag)
                <span class="tag">{{ $tag }}</span>
            @endforeach
        </div>
        <div class="katalog-card-footer">
            <div class="katalog-price-info">
                <span class="katalog-price-label">SEWA PER HARI</span>
                <span class="katalog-price">{{ $productPrice }}</span>
            </div>
            <form action="{{ route('cart.store', $productId) }}" method="POST" style="margin:0;" onclick="event.stopPropagation();">
                @csrf
                <input type="hidden" name="quantity" value="1">
                <button type="submit" class="katalog-cart-btn"><i class="fas fa-shopping-cart"></i></button>
            </form>
        </div>
    </div>
</a>
