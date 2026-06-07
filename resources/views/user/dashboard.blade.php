@extends('layouts.app')

@section('title', 'Dashboard Saya - Gardakala Outdoor')
@section('nav-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-page">
    <div class="dashboard-container">
        
        {{-- MAIN DASHBOARD LAYOUT GRID --}}
        <div class="dash-content-grid">
            
            {{-- LEFT COLUMN: CORE DASHBOARD PORTAL --}}
            <div class="dash-main-column">
                
                {{-- 1. ELEGANT WELCOME BANNER --}}
                <div class="dash-welcome-banner">
                    <div class="banner-content">
                        @php
                            $firstName = explode(' ', Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Petualang')[0];
                        @endphp
                        <span class="banner-tag"><i class="fas fa-compass"></i> PETUALANGAN MENANTI</span>
                        <h1>Halo, {{ $firstName }}!<br>Siap Berpetualang Lagi?</h1>
                        <p>Temukan dan sewa perlengkapan outdoor premium kami dengan mudah untuk ekspedisi Anda selanjutnya.</p>
                        <div class="banner-buttons">
                            <a href="/katalog" class="btn-banner-primary"><i class="fas fa-plus"></i> Sewa Alat Baru</a>
                            <a href="#active-rental-section" class="btn-banner-outline">Pantau Sewa</a>
                        </div>
                    </div>
                    <div class="banner-illustration">
                        <img src="{{ asset('images/hero-mountains.png') }}" alt="Mountains illustration">
                    </div>
                </div>

                {{-- 2. DYNAMIC STATS COUNTER --}}
                <div class="dash-stats">
                    @php
                    $stats = [
                        ['icon' => 'fas fa-campground', 'iconClass' => 'icon-green', 'label' => 'Sewa Aktif', 'number' => str_pad($sewaAktif, 2, '0', STR_PAD_LEFT)],
                        ['icon' => 'far fa-file-alt', 'iconClass' => 'icon-amber', 'label' => 'Total Pesanan', 'number' => str_pad($totalPesanan, 2, '0', STR_PAD_LEFT)],
                        ['icon' => 'far fa-check-circle', 'iconClass' => 'icon-blue', 'label' => 'Selesai', 'number' => str_pad($selesai, 2, '0', STR_PAD_LEFT)],
                    ];
                    @endphp

                    @foreach($stats as $stat)
                        @include('partials.stat-card', ['stat' => $stat])
                    @endforeach
                </div>

                {{-- 3. ACTIVE RENTALS SECTION --}}
                <div class="dash-section-block" id="active-rental-section">
                    <div class="dash-section-header">
                        <h2>Penyewaan Aktif</h2>
                        @if($activeRental)
                            <a href="/riwayat" class="see-all-link">Lihat Semua <i class="fas fa-chevron-right"></i></a>
                        @endif
                    </div>

                    @if($activeRental)
                    @php
                        $tanggalMulai = \Carbon\Carbon::parse($activeRental->tanggal_mulai)->startOfDay();
                        $tanggalSelesai = \Carbon\Carbon::parse($activeRental->tanggal_selesai)->startOfDay();
                        $hariIni = \Carbon\Carbon::now()->startOfDay();
                        
                        $totalHari = max(1, (int) $tanggalMulai->diffInDays($tanggalSelesai));
                        $hariTerlewat = max(0, (int) $tanggalMulai->diffInDays($hariIni, false));
                        $sisaHari = max(0, (int) $hariIni->diffInDays($tanggalSelesai, false));
                        $progress = min(100, round(($hariTerlewat / $totalHari) * 100));
                    @endphp
                    <div class="active-rental-card-premium" id="active-rental">
                        <div class="rental-premium-overlay"></div>
                        <div class="rental-premium-content">
                            <div class="rental-card-header">
                                <div class="rental-info-meta">
                                    <span class="rental-label">KODE TRANSAKSI</span>
                                    <span class="rental-ref">#GK-{{ str_pad($activeRental->id, 4, '0', STR_PAD_LEFT) }}</span>
                                </div>
                                <span class="rental-status-badge-floating">
                                    <i class="fas fa-clock"></i> {{ str_pad($sisaHari, 2, '0', STR_PAD_LEFT) }} Hari Lagi
                                </span>
                            </div>
                            
                            <div class="rental-items-box">
                                <h4>Alat yang sedang disewa:</h4>
                                <ul class="rental-items-list-modern">
                                    @foreach($activeRental->details as $detail)
                                    <li>
                                        <span class="item-icon"><i class="fas fa-campground"></i></span>
                                        <span class="item-details">{{ $detail->product->nama_produk ?? 'Peralatan Outdoor' }} <strong>x {{ $detail->jumlah }} Unit</strong></span>
                                    </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="rental-progress-block">
                                <div class="progress-details">
                                    <span>Tenggat Pengembalian</span>
                                    <span>{{ $progress }}% Selesai</span>
                                </div>
                                <div class="progress-track-modern">
                                    <div class="progress-fill-modern" style="width: {{ $progress }}%;"></div>
                                </div>
                                <div class="progress-dates-row">
                                    <span>{{ $tanggalMulai->format('d M Y') }}</span>
                                    <span>{{ $tanggalSelesai->format('d M Y') }}</span>
                                </div>
                            </div>

                            <div class="rental-actions-premium">
                                @if($activeRentals->count() === 1 && $activeRental->status_perpanjangan !== 'pending')
                                    <a href="{{ route('perpanjangan.form', $activeRental->id) }}" class="btn-action-premium-primary">
                                        <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                                    </a>
                                @elseif($activeRentals->count() > 1)
                                    <a href="{{ route('riwayat') }}?filter=active" class="btn-action-premium-primary">
                                        <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                                    </a>
                                @else
                                    <button disabled class="btn-action-premium-primary disabled-btn">
                                        <i class="fas fa-sync-alt"></i> Perpanjang Sewa (Diproses)
                                    </button>
                                @endif

                                <a href="{{ route('pesanan.nota', $activeRental->id) }}" class="btn-action-premium-outline" target="_blank">
                                    <i class="far fa-file-alt"></i> Nota Digital
                                </a>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="empty-rental-card-modern">
                        <div class="empty-rental-icon">
                            <i class="fas fa-campground"></i>
                        </div>
                        <h3>Belum Ada Sewa Aktif</h3>
                        <p>Mulai rencanakan petualangan serumu dan sewa perlengkapan outdoor premium kami sekarang juga.</p>
                        <a href="/katalog" class="btn-rent-now">Jelajahi Katalog</a>
                    </div>
                    @endif
                </div>

                {{-- 4. RECENT TRANSACTIONS CHRONOLOGY --}}
                <div class="dash-section-block">
                    <div class="dash-section-header">
                        <h2>Transaksi Terakhir</h2>
                        <a href="/riwayat" class="see-all-link">Lihat Semua</a>
                    </div>

                    <div class="transaction-timeline-card">
                        @forelse($recentTransactions as $t)
                        @php
                            $statusStyles = [
                                'menunggu'       => ['class' => 'status-waiting',  'icon' => 'far fa-clock', 'label' => 'Menunggu Pembayaran'],
                                'menunggu_admin' => ['class' => 'status-waiting',  'icon' => 'far fa-hourglass', 'label' => 'Menunggu Konfirmasi'],
                                'diproses'       => ['class' => 'status-active',   'icon' => 'fas fa-box', 'label' => 'Diproses'],
                                'dikirim'        => ['class' => 'status-active',   'icon' => 'fas fa-truck', 'label' => 'Dikirim'],
                                'selesai'        => ['class' => 'status-completed','icon' => 'far fa-check-circle', 'label' => 'Selesai'],
                                'dibatalkan'     => ['class' => 'status-cancelled','icon' => 'fas fa-circle-xmark', 'label' => 'Dibatalkan'],
                            ];
                            $st = $statusStyles[$t->status_transaksi] ?? ['class' => 'status-waiting', 'icon' => 'fas fa-circle-info', 'label' => $t->status_transaksi];
                            $items = $t->details->map(fn($d) => $d->product->nama_produk ?? 'Alat')->implode(', ');
                        @endphp
                        <div class="timeline-row">
                            <div class="timeline-meta">
                                <span class="timeline-ref">#GK-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</span>
                                <span class="timeline-date">{{ $t->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="timeline-details">
                                <h4 class="timeline-items" title="{{ $items }}">{{ Str::limit($items, 56) }}</h4>
                                <span class="status-badge {{ $st['class'] }}"><i class="{{ $st['icon'] }}"></i> {{ $st['label'] }}</span>
                            </div>
                            <div class="timeline-price-action">
                                <span class="timeline-price">Rp {{ number_format($t->total_biaya + $t->denda, 0, ',', '.') }}</span>
                                <a href="{{ route('pesanan.detail', $t->id) }}" class="timeline-action-btn">Detail <i class="fas fa-chevron-right"></i></a>
                            </div>
                        </div>
                        @empty
                        <div class="empty-timeline-block">
                            <i class="far fa-folder-open"></i>
                            <p>Belum ada riwayat transaksi rental.</p>
                            <a href="/katalog">Mulai Menyewa</a>
                        </div>
                        @endforelse
                    </div>
                </div>

            </div>



        </div>
    </div>
</div>
@endsection
