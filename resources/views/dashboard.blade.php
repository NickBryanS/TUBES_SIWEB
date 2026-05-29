@extends('layouts.app')

@section('title', 'Dashboard Saya - Gardakala Outdoor')
@section('nav-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-page">
    <div class="dashboard-container">
        {{-- HEADER SECTION --}}
        <div class="dash-header">
            <div>
                @php
                    $firstName = explode(' ', Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Petualang')[0];
                @endphp
                <h1 class="dash-greeting">Halo, {{ $firstName }}!</h1>
                <p class="dash-greeting-sub">Selamat datang kembali. Pantau jadwal rental, transaksi, dan wishlist petualanganmu di sini.</p>
            </div>
            <div class="dash-header-actions">
                <a href="/katalog" class="btn-dash-primary"><i class="fas fa-plus"></i> Rental Baru</a>
            </div>
        </div>

        {{-- DYNAMIC STATS COUNTER --}}
        <div class="dash-stats" id="dash-stats">
            @php
            $stats = [
                ['icon' => 'fas fa-tent', 'iconClass' => 'icon-green', 'label' => 'Sewa Aktif', 'number' => str_pad($sewaAktif, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'far fa-file-alt', 'iconClass' => 'icon-amber', 'label' => 'Total Pesanan', 'number' => str_pad($totalPesanan, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'far fa-check-circle', 'iconClass' => 'icon-blue', 'label' => 'Selesai', 'number' => str_pad($selesai, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'far fa-clock', 'iconClass' => 'icon-red', 'label' => 'Menunggu Pembayaran', 'number' => str_pad($menungguBayar, 2, '0', STR_PAD_LEFT),
                 'badge' => $menungguBayar > 0 ? 'Segera' : null, 'badgeClass' => 'badge-urgent'],
            ];
            @endphp

            @foreach($stats as $stat)
                @include('partials.stat-card', ['stat' => $stat])
            @endforeach
        </div>

        {{-- MAIN DASHBOARD LAYOUT GRID --}}
        <div class="dash-content-grid">
            {{-- LEFT PANEL: ACTIVE RENTALS --}}
            <div class="dash-left-panel">
                <div class="dash-section-header">
                    <h2>Penyewaan Aktif</h2>
                    @if($activeRental)
                        <a href="/riwayat" class="see-all-link">Lihat Semua Alat <i class="fas fa-chevron-right"></i></a>
                    @endif
                </div>

                @if($activeRental)
                @php
                    $tanggalMulai = \Carbon\Carbon::parse($activeRental->tanggal_mulai);
                    $tanggalSelesai = \Carbon\Carbon::parse($activeRental->tanggal_selesai);
                    $totalHari = max(1, $tanggalMulai->diffInDays($tanggalSelesai));
                    $hariTerlewat = max(0, $tanggalMulai->diffInDays(now()));
                    $sisaHari = max(0, now()->diffInDays($tanggalSelesai, false));
                    $progress = min(100, round(($hariTerlewat / $totalHari) * 100));
                @endphp
                <div class="active-rental-card" id="active-rental">
                    <div class="rental-card-header">
                        <div>
                            <span class="rental-label">KODE TRANSAKSI</span>
                            <span class="rental-ref">#GK-{{ str_pad($activeRental->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <span class="rental-status-badge"><i class="fas fa-spinner fa-spin"></i> Sedang Disewa</span>
                    </div>
                    
                    <div class="rental-items-list">
                        @foreach($activeRental->details as $detail)
                        <div class="rental-item-row">
                            <span class="item-name"><i class="fas fa-tent"></i> {{ $detail->product->nama_produk ?? 'Peralatan Outdoor' }}</span>
                            <span class="item-qty">{{ $detail->jumlah }} Unit</span>
                        </div>
                        @endforeach
                    </div>

                    <div class="rental-period-progress">
                        <div class="period-info-row">
                            <div>
                                <span class="period-label">TANGGAL SEWA</span>
                                <span class="period-dates"><i class="far fa-calendar"></i> {{ $tanggalMulai->format('d M') }} - {{ $tanggalSelesai->format('d M Y') }}</span>
                            </div>
                            <div class="period-remaining">
                                <span class="period-label">SISA HARI</span>
                                <span class="period-days">{{ str_pad($sisaHari, 2, '0', STR_PAD_LEFT) }} Hari Lagi</span>
                            </div>
                        </div>

                        <div class="progress-bar-block">
                            <div class="progress-header">
                                <span>Progres Pengembalian</span>
                                <span>{{ $progress }}%</span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="rental-actions">
                        @if($activeRentals->count() === 1 && $activeRental->status_perpanjangan !== 'pending')
                            <a href="{{ route('perpanjangan.form', $activeRental->id) }}" class="btn-action-primary">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                            </a>
                        @elseif($activeRentals->count() > 1)
                            <a href="{{ route('riwayat') }}?filter=active" class="btn-action-primary">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                            </a>
                        @else
                            <button disabled class="btn-action-primary disabled-btn">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa (Diproses)
                            </button>
                        @endif

                        <a href="{{ route('pesanan.nota', $activeRental->id) }}" class="btn-action-outline" target="_blank">
                            <i class="far fa-file-alt"></i> Nota Digital
                        </a>
                    </div>
                </div>
                @else
                <div class="empty-rental-card">
                    <div class="empty-rental-icon">
                        <i class="fas fa-tent"></i>
                    </div>
                    <h3>Belum ada sewa aktif</h3>
                    <p>Mulai rencanakan petualangan serumu dan sewa perlengkapan outdoor premium kami.</p>
                    <a href="/katalog" class="btn-rent-now">Jelajahi Katalog</a>
                </div>
                @endif
            </div>

            {{-- RIGHT PANEL: PROFILE CARD --}}
            <aside class="dash-right-panel">
                <h2>Profil Saya</h2>
                <div class="profile-card" id="profile-card">
                    <div class="profile-card-avatar">
                        <img src="{{ Auth::user()->url_avatar ?? asset('images/avatar-default.png') }}" alt="{{ Auth::user()->nama_lengkap ?? 'User' }}" class="profile-avatar-img">
                    </div>
                    <div class="profile-card-info">
                        <h3>{{ Auth::user()->nama_lengkap ?? Auth::user()->name ?? 'Petualang' }}</h3>
                        <p class="profile-email">{{ Auth::user()->email }}</p>
                        <span class="profile-joined"><i class="far fa-calendar-alt"></i> Bergabung {{ Auth::user()->created_at->translatedFormat('d M Y') }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}" class="profile-card-logout">
                        @csrf
                        <button type="submit" class="btn-profile-card-logout">
                            <i class="fas fa-sign-out-alt"></i> Logout
                        </button>
                    </form>
                </div>
            </aside>
        </div>

        {{-- RECENT TRANSACTION TABLE --}}
        <div class="dash-transactions">
            <h2>Transaksi Terakhir</h2>
            <div class="transaction-table-card">
                <table class="transaction-table" id="transaction-table">
                    <thead>
                        <tr>
                            <th>ID PESANAN</th>
                            <th>TANGGAL</th>
                            <th>ITEM YANG DISEWA</th>
                            <th>STATUS PROSES</th>
                            <th>TOTAL BIAYA</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $t)
                        @php
                            $statusStyles = [
                                'menunggu'       => ['class' => 'status-waiting',  'icon' => 'far fa-clock', 'label' => 'Menunggu Pembayaran'],
                                'menunggu_admin' => ['class' => 'status-waiting',  'icon' => 'far fa-hourglass', 'label' => 'Menunggu Konfirmasi'],
                                'diproses'       => ['class' => 'status-active',   'icon' => 'fas fa-box', 'label' => 'Diproses'],
                                'dikirim'        => ['class' => 'status-active',   'icon' => 'fas fa-truck', 'label' => 'Dikirim'],
                                'selesai'        => ['class' => 'status-completed','icon' => 'far fa-circle-check', 'label' => 'Selesai'],
                                'dibatalkan'     => ['class' => 'status-cancelled','icon' => 'fas fa-circle-xmark', 'label' => 'Dibatalkan'],
                            ];
                            $st = $statusStyles[$t->status_transaksi] ?? ['class' => 'status-waiting', 'icon' => 'fas fa-circle-info', 'label' => $t->status_transaksi];
                            $items = $t->details->map(fn($d) => $d->product->nama_produk ?? 'Alat')->implode(', ');
                        @endphp
                        <tr>
                            <td class="order-ref-code">#GK-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $t->created_at->format('d M Y') }}</td>
                            <td class="items-cell" title="{{ $items }}">{{ Str::limit($items, 44) }}</td>
                            <td><span class="status-badge {{ $st['class'] }}"><i class="{{ $st['icon'] }}"></i> {{ $st['label'] }}</span></td>
                            <td class="price-val-col">Rp {{ number_format($t->total_biaya + $t->denda, 0, ',', '.') }}</td>
                            <td class="actions-cell">
                                <a href="{{ route('pesanan.detail', $t->id) }}" class="table-action-link">Lihat Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="empty-table-row">
                                <i class="far fa-folder-open"></i>
                                <p>Belum ada riwayat transaksi rental.</p>
                                <a href="/katalog">Mulai Menyewa</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
