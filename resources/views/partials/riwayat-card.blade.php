{{-- Redesigned Riwayat Card (Transaction History) --}}
@php
    $rawStatus = $trx['rawStatus'] ?? 'menunggu';
    $isPickup = ($trx['metode_pengambilan'] ?? 'pickup') === 'pickup';
    $isCancelled = ($rawStatus === 'dibatalkan');
    
    // Status progress definition for Pickup (4 steps)
    // Steps: Dipesan -> Dibayar -> Dipinjam -> Dikembalikan
    $step1 = false; // Dipesan
    $step2 = false; // Dibayar
    $step3 = false; // Dipinjam
    $step4 = false; // Dikembalikan

    if (!$isCancelled) {
        $step1 = true;
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

    // Status progress definition for Delivery (5 steps)
    // Steps: Pesanan Dibuat -> Barang Disiapkan -> Barang Sedang Diantar -> Barang Diterima -> Selesai
    $delivStep1 = false; // Pesanan Dibuat
    $delivStep2 = false; // Barang Disiapkan
    $delivStep3 = false; // Barang Sedang Diantar
    $delivStep4 = false; // Barang Diterima
    $delivStep5 = false; // Selesai

    if (!$isCancelled) {
        $delivStep1 = true;
        if (in_array($rawStatus, ['diproses', 'dikirim', 'selesai'])) {
            $delivStep2 = true;
        }
        if (in_array($rawStatus, ['dikirim', 'selesai'])) {
            $delivStep3 = true;
        }
        if (($rawStatus === 'dikirim' && ($trx['barang_diterima'] ?? false)) || $rawStatus === 'selesai') {
            $delivStep4 = true;
        }
        if ($rawStatus === 'selesai') {
            $delivStep5 = true;
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
                    @if($isPickup)
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
                    @else
                        @php
                            $delivWidth = '0%';
                            if ($delivStep5) {
                                $delivWidth = '100%';
                            } elseif ($delivStep4) {
                                $delivWidth = '75%';
                            } elseif ($delivStep3) {
                                $delivWidth = '50%';
                            } elseif ($delivStep2) {
                                $delivWidth = '25%';
                            }
                        @endphp
                        <div class="tracking-progress-bar">
                            <div class="progress-line-fill" style="width: {{ $delivWidth }}"></div>
                        </div>
                        <div class="tracking-steps">
                            {{-- Step 1 --}}
                            <div class="tracking-step {{ $delivStep1 ? 'active' : '' }}">
                                <div class="step-dot">
                                    @if($delivStep2) <i class="fas fa-check"></i> @else 1 @endif
                                </div>
                                <span class="step-label">Pesanan Dibuat</span>
                            </div>
                            {{-- Step 2 --}}
                            <div class="tracking-step {{ $delivStep2 ? 'active' : '' }}">
                                <div class="step-dot">
                                    @if($delivStep3) <i class="fas fa-check"></i> @else 2 @endif
                                </div>
                                <span class="step-label">Barang Disiapkan</span>
                            </div>
                            {{-- Step 3 --}}
                            <div class="tracking-step {{ $delivStep3 ? 'active' : '' }}">
                                <div class="step-dot">
                                    @if($delivStep4) <i class="fas fa-check"></i> @else 3 @endif
                                </div>
                                <span class="step-label">Barang Sedang Diantar</span>
                            </div>
                            {{-- Step 4 --}}
                            <div class="tracking-step {{ $delivStep4 ? 'active' : '' }}">
                                <div class="step-dot">
                                    @if($delivStep5) <i class="fas fa-check"></i> @else 4 @endif
                                </div>
                                <span class="step-label">Barang Diterima</span>
                            </div>
                            {{-- Step 5 --}}
                            <div class="tracking-step {{ $delivStep5 ? 'active' : '' }}">
                                <div class="step-dot">
                                    @if($delivStep5) <i class="fas fa-check"></i> @else 5 @endif
                                </div>
                                <span class="step-label">Selesai</span>
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <div class="cancelled-notice">
                    <i class="fas fa-circle-exclamation"></i>
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
