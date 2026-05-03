{{-- Partials: Riwayat Card (Transaction History)
    @param $trx - array with: id, image, ref, name, items, status, statusClass, statusIcon, price, actions[]
--}}
<div class="riwayat-card" data-status="{{ $trx['filterStatus'] }}" id="riwayat-{{ $trx['id'] }}">
    <div class="riwayat-card-image">
        <img src="{{ asset($trx['image']) }}" alt="{{ $trx['name'] }}">
    </div>
    <div class="riwayat-card-content">
        <div class="riwayat-card-top">
            <div>
                <span class="riwayat-ref">REF: {{ $trx['ref'] }}</span>
                <h3>{{ $trx['name'] }}</h3>
                <p class="riwayat-items">{{ $trx['items'] }}</p>
            </div>
            <span class="riwayat-status {{ $trx['statusClass'] }}"><i class="{{ $trx['statusIcon'] }}"></i> {{ $trx['status'] }}</span>
        </div>
        <div class="riwayat-card-bottom">
            <div class="riwayat-price-info">
                <span class="riwayat-price-label">Total Pembayaran</span>
                <span class="riwayat-price">{{ $trx['price'] }}</span>
            </div>
            <div class="riwayat-actions">
                @foreach($trx['actions'] as $action)
                    <a href="{{ $action['url'] }}" class="btn-riwayat {{ $action['class'] }}">{{ $action['label'] }}</a>
                @endforeach
            </div>
        </div>
    </div>
</div>
