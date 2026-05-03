{{-- Partials: Product Card (Katalog)
    @param $product - array with: id, image, name, rating, desc, tags, price, badge, badgeClass
--}}
<a href="/produk/{{ $product['id'] }}" class="katalog-card" id="katalog-item-{{ $product['id'] }}">
    <div class="katalog-card-image">
        @if(!empty($product['badge']))
            <span class="katalog-badge {{ $product['badgeClass'] ?? '' }}">{{ $product['badge'] }}</span>
        @endif
        <img src="{{ asset($product['image']) }}" alt="{{ $product['name'] }}">
    </div>
    <div class="katalog-card-info">
        <div class="katalog-card-title-row">
            <h3>{{ $product['name'] }}</h3>
            <div class="katalog-rating"><i class="fas fa-star"></i> {{ $product['rating'] }}</div>
        </div>
        <p class="katalog-card-desc">{{ $product['desc'] }}</p>
        <div class="katalog-card-tags">
            @foreach($product['tags'] as $tag)
                <span class="tag">{{ $tag }}</span>
            @endforeach
        </div>
        <div class="katalog-card-footer">
            <div class="katalog-price-info">
                <span class="katalog-price-label">SEWA PER HARI</span>
                <span class="katalog-price">{{ $product['price'] }}</span>
            </div>
            <button class="katalog-cart-btn"><i class="fas fa-shopping-cart"></i></button>
        </div>
    </div>
</a>
