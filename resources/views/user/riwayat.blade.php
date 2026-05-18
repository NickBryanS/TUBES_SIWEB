@extends('layouts.app')

@section('title', 'Riwayat Transaksi - Gardakala Outdoor')
@section('nav-rental', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/riwayat.css') }}">
@endsection

@section('content')
<div class="riwayat-page">
    <div class="riwayat-container">
        <h1 class="riwayat-title">Riwayat Transaksi</h1>
        <p class="riwayat-subtitle">Kelola penyewaan peralatan outdoor Anda dan pantau status petualangan berikutnya.</p>

        {{-- FILTER TABS --}}
        <div class="riwayat-tabs" id="riwayat-tabs">
            <button class="riwayat-tab active" data-filter="all">Semua</button>
            <button class="riwayat-tab" data-filter="active">Aktif</button>
            <button class="riwayat-tab" data-filter="done">Selesai</button>
            <button class="riwayat-tab" data-filter="cancelled">Dibatalkan</button>
        </div>

        {{-- TRANSACTION LIST (using partial) --}}
        <div class="riwayat-list" id="riwayat-list">
            @php
            $transactions = [
                [
                    'id' => 1, 'filterStatus' => 'active',
                    'image' => 'images/tent-expedition.png',
                    'ref' => 'GRK-20240521-199', 'name' => 'Paket Ekspedisi Everest Solo',
                    'items' => 'Tenda Ultralight, Matras Angin, Sleeping Bag 0°C',
                    'status' => 'Menunggu', 'statusClass' => 'status-waiting', 'statusIcon' => 'fas fa-clock',
                    'price' => 'Rp 1.250.000',
                    'actions' => [
                        ['url' => '#', 'class' => 'btn-pay', 'label' => 'Bayar Sekarang'],
                        ['url' => '/pesanan/1', 'class' => 'btn-detail', 'label' => 'Lihat Detail'],
                    ]
                ],
                [
                    'id' => 2, 'filterStatus' => 'active',
                    'image' => 'images/backpack-product.png',
                    'ref' => 'GRK-20240618-113', 'name' => 'Hiking Essentials Pack',
                    'items' => 'Carrier 60L, Trekking Poles, Headlamp 600lm',
                    'status' => 'Menunggu Admin', 'statusClass' => 'status-admin', 'statusIcon' => 'fas fa-hourglass-half',
                    'price' => 'Rp 450.000',
                    'actions' => [
                        ['url' => '/pesanan/1', 'class' => 'btn-detail', 'label' => 'Lihat Detail'],
                    ]
                ],
                [
                    'id' => 3, 'filterStatus' => 'done',
                    'image' => 'images/sleepingbag-product.png',
                    'ref' => 'GRK-20240415-056', 'name' => 'Camping Weekend Set',
                    'items' => 'Sleeping Bag, Matras, Hammock',
                    'status' => 'Selesai', 'statusClass' => 'status-done-badge', 'statusIcon' => 'fas fa-check-circle',
                    'price' => 'Rp 450.000',
                    'actions' => [
                        ['url' => '/riwayat', 'class' => 'btn-extend', 'label' => 'Perpanjang Sewa'],
                        ['url' => '/pesanan/1', 'class' => 'btn-detail', 'label' => 'Lihat Detail'],
                    ]
                ],
                [
                    'id' => 4, 'filterStatus' => 'done',
                    'image' => 'images/cooking-set.png',
                    'ref' => 'GRK-20240415-096', 'name' => 'Cooking System Kit',
                    'items' => 'Jetboil Stoves, Titanium Set, Fuel Canisters',
                    'status' => 'Selesai', 'statusClass' => 'status-done-badge', 'statusIcon' => 'fas fa-check-circle',
                    'price' => 'Rp 320.000',
                    'actions' => [
                        ['url' => '/riwayat', 'class' => 'btn-extend', 'label' => 'Perpanjang Sewa'],
                        ['url' => '/pesanan/1', 'class' => 'btn-detail', 'label' => 'Lihat Detail'],
                    ]
                ],
            ];
            @endphp

            @foreach($transactions as $trx)
                @include('partials.riwayat-card', ['trx' => $trx])
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.querySelectorAll('.riwayat-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.riwayat-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const filter = this.dataset.filter;
        document.querySelectorAll('.riwayat-card').forEach(function(card) {
            if (filter === 'all' || card.dataset.status === filter) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });
});
</script>
@endsection
