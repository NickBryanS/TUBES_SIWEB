@extends('layouts.app')

@section('title', 'Dashboard - Gardakala Outdoor')
@section('nav-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
@endsection

@section('content')
<div class="dashboard-page">
    <div class="dashboard-container">
        {{-- HEADER --}}
        <div class="dash-header">
            <div>
                <h1 class="dash-greeting">Halo, Petualang!</h1>
                <p class="dash-greeting-sub">Pantau perlengkapan dan riwayat petualanganmu di sini.</p>
            </div>
        </div>

        {{-- STATS CARDS (dynamic from database) --}}
        <div class="dash-stats" id="dash-stats">
            @php
            $stats = [
                ['icon' => 'fas fa-campground', 'iconClass' => 'icon-green', 'label' => 'Sewa Aktif', 'number' => str_pad($sewaAktif, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'fas fa-clipboard-list', 'iconClass' => 'icon-amber', 'label' => 'Total Pesanan', 'number' => str_pad($totalPesanan, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'fas fa-check-circle', 'iconClass' => 'icon-blue', 'label' => 'Selesai', 'number' => str_pad($selesai, 2, '0', STR_PAD_LEFT)],
                ['icon' => 'fas fa-clock', 'iconClass' => 'icon-red', 'label' => 'Menunggu Pembayaran', 'number' => str_pad($menungguBayar, 2, '0', STR_PAD_LEFT),
                 'badge' => $menungguBayar > 0 ? 'Segera' : null, 'badgeClass' => 'badge-urgent'],
            ];
            @endphp

            @foreach($stats as $stat)
                @include('partials.stat-card', ['stat' => $stat])
            @endforeach
        </div>

        {{-- MAIN CONTENT GRID --}}
        <div class="dash-content-grid">
            {{-- LEFT: ACTIVE RENTAL --}}
            <div class="dash-left">
                <div class="dash-section-header">
                    <h2>Sedang Disewa</h2>
                    <a href="/riwayat" class="see-all-link">Lihat Semua Alat <i class="fas fa-arrow-right"></i></a>
                </div>

                @if($activeRental)
                @php
                    $tanggalMulai = \Carbon\Carbon::parse($activeRental->tanggal_mulai);
                    $tanggalSelesai = \Carbon\Carbon::parse($activeRental->tanggal_selesai);
                    $totalHari = $tanggalMulai->diffInDays($tanggalSelesai);
                    $hariTerlewat = $tanggalMulai->diffInDays(now());
                    $sisaHari = max(0, now()->diffInDays($tanggalSelesai, false));
                    $progress = $totalHari > 0 ? min(100, round(($hariTerlewat / $totalHari) * 100)) : 0;
                @endphp
                <div class="active-rental-card" id="active-rental">
                    <div class="rental-card-header">
                        <div>
                            <span class="rental-label">RINGKASAN SEWA AKTIF</span>
                            <span class="rental-ref">#GK-{{ str_pad($activeRental->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <span class="rental-status status-active"><i class="fas fa-circle"></i> Aktif Berjalan</span>
                    </div>
                    <div class="rental-items-list">
                        @foreach($activeRental->details as $detail)
                        <div class="rental-item-row">
                            <span><i class="fas fa-campground"></i> {{ $detail->product->nama_produk ?? 'Produk' }}</span>
                            <span>{{ $detail->jumlah }} Unit</span>
                        </div>
                        @endforeach
                    </div>
                    <div class="rental-period-bar">
                        <div class="period-info">
                            <div>
                                <span class="period-label">MASA SEWA</span>
                                <span class="period-dates"><i class="fas fa-calendar"></i> {{ $tanggalMulai->format('d M') }} - {{ $tanggalSelesai->format('d M Y') }}</span>
                            </div>
                            <div class="period-remaining">
                                <span class="period-label">SISA WAKTU</span>
                                <span class="period-days">{{ str_pad($sisaHari, 2, '0', STR_PAD_LEFT) }} Hari</span>
                            </div>
                        </div>
                        <div class="progress-bar-container">
                            <span class="progress-label">PROGRES PENGEMBALIAN</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $progress }}%;"></div>
                            </div>
                            <span class="progress-pct">{{ $progress }}%</span>
                        </div>
                    </div>
                    <div class="rental-actions">
                        {{-- PERPANJANG: smart redirect --}}
                        @if($activeRentals->count() === 1 && $activeRental->status_perpanjangan !== 'pending')
                            <a href="{{ route('perpanjangan.form', $activeRental->id) }}" class="btn-rental-action btn-extend">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                            </a>
                        @elseif($activeRentals->count() > 1)
                            <a href="{{ route('riwayat') }}?filter=active" class="btn-rental-action btn-extend">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                            </a>
                        @else
                            <span class="btn-rental-action btn-extend" style="opacity: 0.5; cursor: not-allowed;">
                                <i class="fas fa-sync-alt"></i> Perpanjang Sewa
                            </span>
                        @endif

                        {{-- NOTA DIGITAL --}}
                        <a href="{{ route('pesanan.nota', $activeRental->id) }}" class="btn-rental-action btn-nota" target="_blank">
                            <i class="fas fa-file-alt"></i> Unduh Nota Digital
                        </a>
                    </div>
                </div>
                @else
                <div class="active-rental-card" id="active-rental" style="text-align: center; padding: 40px;">
                    <i class="fas fa-campground" style="font-size: 2.5rem; color: rgba(255,255,255,0.15); margin-bottom: 12px;"></i>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.95rem;">Tidak ada penyewaan aktif saat ini.</p>
                    <a href="/katalog" style="color: #e8a838; text-decoration: none; font-size: 0.9rem; margin-top: 8px; display: inline-block;">
                        <i class="fas fa-arrow-right"></i> Jelajahi Katalog
                    </a>
                </div>
                @endif
            </div>

            {{-- RIGHT: ADVENTURE --}}
            <div class="dash-right">
                <h2>Petualangan Terdekat</h2>
                <div class="adventure-card" id="adventure-card">
                    <div class="adventure-image">
                        <img src="{{ asset('images/mountain-adventure.png') }}" alt="Gunung Gede">
                    </div>
                    <div class="adventure-info">
                        <h3>Ekspedisi Gunung Gede</h3>
                        <p><i class="fas fa-calendar"></i> 26 Okt - 28 Okt 2024</p>
                        <p><i class="fas fa-map-marker-alt"></i> Basecamp Cibodas</p>
                        <a href="#" class="adventure-btn">Lihat Rencana Perjalanan</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TRANSACTION HISTORY (real-time from database) --}}
        <div class="dash-transactions">
            <h2>Transaksi Terakhir</h2>
            <div class="transaction-table-wrapper">
                <table class="transaction-table" id="transaction-table">
                    <thead>
                        <tr>
                            <th>ID PESANAN</th>
                            <th>TANGGAL</th>
                            <th>ITEM</th>
                            <th>STATUS</th>
                            <th>TOTAL HARGA</th>
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentTransactions as $t)
                        @php
                            $statusStyles = [
                                'menunggu'       => ['class' => 'status-pending',  'icon' => 'fas fa-exclamation-circle', 'label' => 'Menunggu Bayar'],
                                'menunggu_admin' => ['class' => 'status-pending',  'icon' => 'fas fa-hourglass-half',     'label' => 'Menunggu Verifikasi'],
                                'diproses'       => ['class' => 'status-approved', 'icon' => 'fas fa-check-circle',       'label' => 'Diproses'],
                                'dikirim'        => ['class' => 'status-shipped',  'icon' => 'fas fa-truck',              'label' => 'Dikirim'],
                                'selesai'        => ['class' => 'status-done',     'icon' => 'fas fa-check-double',       'label' => 'Selesai'],
                                'dibatalkan'     => ['class' => 'status-cancelled','icon' => 'fas fa-ban',                'label' => 'Dibatalkan'],
                            ];
                            $st = $statusStyles[$t->status_transaksi] ?? ['class' => 'status-pending', 'icon' => 'fas fa-info-circle', 'label' => $t->status_transaksi];
                            $items = $t->details->map(fn($d) => $d->product->nama_produk ?? 'Produk')->implode(', ');
                        @endphp
                        <tr>
                            <td class="order-id">#GK-{{ str_pad($t->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $t->created_at->format('d M Y') }}</td>
                            <td>{{ Str::limit($items, 40) }}</td>
                            <td><span class="status-badge {{ $st['class'] }}"><i class="{{ $st['icon'] }}"></i> {{ $st['label'] }}</span></td>
                            <td class="price-col">Rp {{ number_format($t->total_biaya + $t->denda, 0, ',', '.') }}</td>
                            <td>
                                @if(in_array($t->status_transaksi, ['diproses', 'dikirim']))
                                    <a href="{{ route('pesanan.detail', $t->id) }}" class="table-link">Lacak</a>
                                @endif
                                <a href="{{ route('pesanan.detail', $t->id) }}" class="table-link">Detail</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" style="text-align: center; color: rgba(255,255,255,0.4); padding: 30px;">
                                Belum ada transaksi. <a href="/katalog" style="color: #e8a838;">Mulai belanja</a>
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
