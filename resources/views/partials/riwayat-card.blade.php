{{-- Redesigned Riwayat Card (Transaction History) --}}
@php
    $rawStatus = $trx['rawStatus'] ?? 'menunggu';
    
    // Status progress definition
    // Steps: Dipesan -> Dibayar -> Dipinjam -> Dikembalikan
    $step1 = false; // Dipesan
    $step2 = false; // Dibayar
    $step3 = false; // Dipinjam
    $step4 = false; // Dikembalikan
    $isCancelled = ($rawStatus === 'dibatalkan');

    if (!$isCancelled) {
        $step1 = true; // Dipesan is always complete if not cancelled
        
        if (in_array($rawStatus, ['menunggu_admin', 'diproses', 'dikirim', 'selesai'])) {
            $step2 = true;
        }
        if (in_array($rawStatus, ['diproses', 'dikirim', 'selesai'])) {
            $step3 = true;
        }
        if ($rawStatus === 'selesai') {
            $step4 = true;
        }
    }

    // Status banner class & color helper
    $statusText = $trx['status'];
    $statusBadgeClass = 'status-pending';

    if ($rawStatus === 'selesai') {
        $statusBadgeClass = 'status-completed';
        $statusText = 'Selesai';
    } elseif (in_array($rawStatus, ['diproses', 'dikirim'])) {
        $statusBadgeClass = 'status-active';
        $statusText = 'Sedang Disewa';
    } elseif ($rawStatus === 'dibatalkan') {
        $statusBadgeClass = 'status-cancelled';
        $statusText = 'Dibatalkan';
    }
@endphp

<div class="riwayat-card" data-status="{{ $trx['filterStatus'] }}" id="riwayat-{{ $trx['id'] }}">
    <div class="riwayat-card-main">
        {{-- Image --}}
        <div class="riwayat-card-image">
            <img src="{{ asset($trx['image']) }}" alt="{{ $trx['name'] }}">
        </div>

        {{-- Content Info --}}
        <div class="riwayat-card-content">
            <div class="riwayat-card-header">
                <div>
                    <span class="riwayat-ref">REF: {{ $trx['ref'] }}</span>
                    <h3 class="riwayat-title">{{ $trx['name'] }}</h3>
                    <p class="riwayat-items">{{ $trx['items'] }}</p>
                </div>
                <div>
                    <span class="status-badge {{ $statusBadgeClass }}">{{ $statusText }}</span>
                </div>
            </div>

            {{-- Order Tracking Progress --}}
            @if(!$isCancelled)
                <div class="order-tracking-wrapper">
                    <div class="tracking-progress-bar">
                        <div class="progress-line-fill" style="width: {{ $step4 ? '100%' : ($step3 ? '66%' : ($step2 ? '33%' : '0%')) }}"></div>
                    </div>
                    <div class="tracking-steps">
                        {{-- Step 1 --}}
                        <div class="tracking-step {{ $step1 ? 'active' : '' }}">
                            <div class="step-dot">
                                @if($step2) <i class="fas fa-check"></i> @else 1 @endif
                            </div>
                            <span class="step-label">Dipesan</span>
                        </div>
                        {{-- Step 2 --}}
                        <div class="tracking-step {{ $step2 ? 'active' : '' }}">
                            <div class="step-dot">
                                @if($step3) <i class="fas fa-check"></i> @else 2 @endif
                            </div>
                            <span class="step-label">Dibayar</span>
                        </div>
                        {{-- Step 3 --}}
                        <div class="tracking-step {{ $step3 ? 'active' : '' }}">
                            <div class="step-dot">
                                @if($step4) <i class="fas fa-check"></i> @else 3 @endif
                            </div>
                            <span class="step-label">Dipinjam</span>
                        </div>
                        {{-- Step 4 --}}
                        <div class="tracking-step {{ $step4 ? 'active' : '' }}">
                            <div class="step-dot">
                                @if($step4) <i class="fas fa-check"></i> @else 4 @endif
                            </div>
                            <span class="step-label">Dikembalikan</span>
                        </div>
                    </div>
                </div>
            @else
                <div class="cancelled-notice">
                    <i class="far fa-exclamation-circle"></i>
                    <span>Transaksi ini telah dibatalkan.</span>
                </div>
            @endif
        </div>

        {{-- Price & Actions --}}
        <div class="riwayat-card-sidebar">
            <div class="price-info">
                <span class="price-label">Total Biaya</span>
                <span class="price-val">{{ $trx['price'] }}</span>
            </div>
            <div class="action-buttons">
                @foreach($trx['actions'] as $action)
                    @if(strpos($action['class'], 'btn-pay') !== false)
                        <a href="{{ $action['url'] }}" class="btn-action-primary">Bayar</a>
                    @else
                        <a href="{{ $action['url'] }}" class="btn-action-outline">Lihat Detail</a>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
