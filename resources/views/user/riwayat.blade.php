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
            $mappedTransactions = [];
            foreach($transactions as $t) {
                $filterStatus = 'active';
                if ($t->status_transaksi === 'selesai') $filterStatus = 'done';
                if ($t->status_transaksi === 'dibatalkan') $filterStatus = 'cancelled';

                $firstDetail = $t->details->first();
                $image = $firstDetail && $firstDetail->product ? $firstDetail->product->url_gambar : 'images/placeholder.png';
                $name = $firstDetail && $firstDetail->product ? $firstDetail->product->nama_produk : 'Pesanan';

                $items = $t->details->map(function($d) {
                    return $d->product ? $d->product->nama_produk . ' (x'.$d->jumlah.')' : 'Produk (x'.$d->jumlah.')';
                })->implode(', ');

                $statusMap = [
                    'menunggu' => ['label' => 'Menunggu Pembayaran', 'class' => 'status-waiting', 'icon' => 'fas fa-clock'],
                    'menunggu_admin' => ['label' => 'Menunggu Konfirmasi', 'class' => 'status-admin', 'icon' => 'fas fa-hourglass-half'],
                    'diproses' => ['label' => 'Diproses', 'class' => 'status-admin', 'icon' => 'fas fa-box'],
                    'dikirim' => ['label' => 'Dikirim / Bisa Diambil', 'class' => 'status-waiting', 'icon' => 'fas fa-truck'],
                    'selesai' => ['label' => 'Selesai', 'class' => 'status-done-badge', 'icon' => 'fas fa-check-circle'],
                    'dibatalkan' => ['label' => 'Dibatalkan', 'class' => 'status-cancelled-badge', 'icon' => 'fas fa-ban'],
                ];

                $statusInfo = $statusMap[$t->status_transaksi] ?? ['label' => str_replace('_', ' ', $t->status_transaksi), 'class' => 'status-waiting', 'icon' => 'fas fa-info-circle'];
                
                $actions = [];
                if ($t->status_transaksi === 'menunggu') {
                    $actions[] = ['url' => route('konfirmasi', $t->id), 'class' => 'btn-pay', 'label' => 'Bayar Sekarang'];
                }
                $actions[] = ['url' => route('pesanan.detail', $t->id), 'class' => 'btn-detail', 'label' => 'Lihat Detail'];

                $mappedTransactions[] = [
                    'id' => $t->id,
                    'filterStatus' => $filterStatus,
                    'image' => $image,
                    'ref' => 'GK-' . str_pad($t->id, 4, '0', STR_PAD_LEFT),
                    'name' => $name . ($t->details->count() > 1 ? ' + lainnya' : ''),
                    'items' => $items,
                    'status' => $statusInfo['label'],
                    'statusClass' => $statusInfo['class'],
                    'statusIcon' => $statusInfo['icon'],
                    'price' => 'Rp ' . number_format($t->total_biaya + $t->denda, 0, ',', '.'),
                    'actions' => $actions
                ];
            }
            @endphp

            @foreach($mappedTransactions as $trx)
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
