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

        {{-- STATS CARDS (using partial) --}}
        <div class="dash-stats" id="dash-stats">
            @php
            $stats = [
                ['icon' => 'fas fa-campground', 'iconClass' => 'icon-green', 'label' => 'Sewa Aktif', 'number' => '03'],
                ['icon' => 'fas fa-clipboard-list', 'iconClass' => 'icon-amber', 'label' => 'Total Pesanan', 'number' => '12'],
                ['icon' => 'fas fa-check-circle', 'iconClass' => 'icon-blue', 'label' => 'Selesai', 'number' => '08'],
                ['icon' => 'fas fa-clock', 'iconClass' => 'icon-red', 'label' => 'Menunggu Pembayaran', 'number' => '01', 'badge' => 'Segera', 'badgeClass' => 'badge-urgent'],
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

                <div class="active-rental-card" id="active-rental">
                    <div class="rental-card-header">
                        <div>
                            <span class="rental-label">RINGKASAN SEWA AKTIF</span>
                            <span class="rental-ref">#INV-2024-098210</span>
                        </div>
                        <span class="rental-status status-active"><i class="fas fa-circle"></i> Aktif Berjalan</span>
                    </div>
                    <div class="rental-items-list">
                        <div class="rental-item-row">
                            <span><i class="fas fa-campground"></i> The North Face Summit Tent</span>
                            <span>1 Unit</span>
                        </div>
                        <div class="rental-item-row">
                            <span><i class="fas fa-hiking"></i> Osprey Aether 70L</span>
                            <span>1 Unit</span>
                        </div>
                        <div class="rental-item-row">
                            <span><i class="fas fa-bed"></i> Sleeping Bag Extreme</span>
                            <span>2 Unit</span>
                        </div>
                    </div>
                    <div class="rental-period-bar">
                        <div class="period-info">
                            <div>
                                <span class="period-label">MASA SEWA</span>
                                <span class="period-dates"><i class="fas fa-calendar"></i> 10 Okt - 17 Okt 2024</span>
                            </div>
                            <div class="period-remaining">
                                <span class="period-label">SISA WAKTU</span>
                                <span class="period-days">02Hari</span>
                            </div>
                        </div>
                        <div class="progress-bar-container">
                            <span class="progress-label">PROGRES PENGEMBALIAN</span>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: 15%;"></div>
                            </div>
                            <span class="progress-pct">15%</span>
                        </div>
                    </div>
                    <div class="rental-actions">
                        <a href="/riwayat" class="btn-rental-action btn-extend">
                            <i class="fas fa-sync-alt"></i> Perpanjang Semua
                        </a>
                        <a href="#" class="btn-rental-action btn-nota">
                            <i class="fas fa-file-alt"></i> Unduh Nota Digital
                        </a>
                    </div>
                </div>
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

        {{-- TRANSACTION HISTORY --}}
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
                        <tr>
                            <td class="order-id">#GK-88210</td>
                            <td>12 Okt 2024</td>
                            <td>Osprey Aether 70L, Sleeping Bag</td>
                            <td><span class="status-badge status-approved"><i class="fas fa-check-circle"></i> Diproses</span></td>
                            <td class="price-col">Rp 450.000</td>
                            <td><a href="/pesanan/1" class="table-link">Lacak</a> <a href="/pesanan/1" class="table-link">Detail</a></td>
                        </tr>
                        <tr>
                            <td class="order-id">#GK-88205</td>
                            <td>08 Okt 2024</td>
                            <td>Summit Tent, Portable Stove</td>
                            <td><span class="status-badge status-shipped"><i class="fas fa-truck"></i> Dikirimkn</span></td>
                            <td class="price-col">Rp 820.000</td>
                            <td><a href="/pesanan/1" class="table-link">Detail</a></td>
                        </tr>
                        <tr>
                            <td class="order-id">#GK-88192</td>
                            <td>05 Okt 2024</td>
                            <td>Hiking Boots Grade A</td>
                            <td><span class="status-badge status-done"><i class="fas fa-check-double"></i> Selesai</span></td>
                            <td class="price-col">Rp 120.000</td>
                            <td><a href="/pesanan/1" class="table-link">Detail</a></td>
                        </tr>
                        <tr>
                            <td class="order-id">#GK-88188</td>
                            <td>02 Okt 2024</td>
                            <td>Waterproof Shell Jacket</td>
                            <td><span class="status-badge status-pending"><i class="fas fa-exclamation-circle"></i> Menunggu Verifikasi</span></td>
                            <td class="price-col">Rp 150.000</td>
                            <td><a href="/pesanan/1" class="table-link">Detail</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
