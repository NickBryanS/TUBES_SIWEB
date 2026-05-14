@extends('admin.layouts.admin')

@section('title', 'Pusat Notifikasi - Admin GKDL (Full)')
@section('sidebar-notifikasi', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/notifikasi.css') }}">
@endsection

@section('content')
<div class="notifikasi-page">
    {{-- HEADER --}}
    <div class="notifikasi-header">
        <div class="notifikasi-header-text">
            <h1 class="notifikasi-title">Pusat Notifikasi</h1>
            <p class="notifikasi-subtitle">Kelola permintaan penyewaan dan aktivitas pelanggan GKDL Outdoor.</p>
        </div>
        <div class="notifikasi-header-actions">
            <form action="{{ route('admin.notifikasi.markAllRead') }}" method="POST">
                @csrf
                <button type="submit" class="btn-mark-read" id="btn-mark-all-read">
                    <i class="fas fa-check-double"></i>
                    Tandai Semua Dibaca
                </button>
            </form>
        </div>
    </div>

    {{-- TABS --}}
    <div class="notifikasi-tabs" id="notifikasi-tabs">
        <a href="{{ route('admin.notifikasi.index', ['filter' => 'semua']) }}"
           class="notif-tab {{ $filter === 'semua' ? 'active' : '' }}" id="tab-semua">
            Semua
            @if($counts['semua'] > 0) <span class="tab-badge">{{ $counts['semua'] }}</span> @endif
        </a>
        <a href="{{ route('admin.notifikasi.index', ['filter' => 'pesanan']) }}"
           class="notif-tab {{ $filter === 'pesanan' ? 'active' : '' }}" id="tab-pesanan">
            Pesanan
            @if($counts['pesanan'] > 0) <span class="tab-badge">{{ $counts['pesanan'] }}</span> @endif
        </a>
        <a href="{{ route('admin.notifikasi.index', ['filter' => 'pembayaran']) }}"
           class="notif-tab {{ $filter === 'pembayaran' ? 'active' : '' }}" id="tab-pembayaran">
            Pembayaran
            @if($counts['pembayaran'] > 0) <span class="tab-badge">{{ $counts['pembayaran'] }}</span> @endif
        </a>
        <a href="{{ route('admin.notifikasi.index', ['filter' => 'pengembalian']) }}"
           class="notif-tab {{ $filter === 'pengembalian' ? 'active' : '' }}" id="tab-pengembalian">
            Pengembalian
            @if($counts['pengembalian'] > 0) <span class="tab-badge">{{ $counts['pengembalian'] }}</span> @endif
        </a>
        <a href="{{ route('admin.notifikasi.index', ['filter' => 'sistem']) }}"
           class="notif-tab {{ $filter === 'sistem' ? 'active' : '' }}" id="tab-sistem">
            Sistem
            @if($counts['sistem'] > 0) <span class="tab-badge">{{ $counts['sistem'] }}</span> @endif
        </a>
    </div>

    {{-- NOTIFICATION LIST --}}
    <div class="notifikasi-list" id="notifikasi-list">
        @forelse($notifications as $notif)
        <div class="notif-item {{ $notif['read'] ? 'notif-read' : 'notif-unread' }}" data-type="{{ $notif['type'] }}" id="notif-{{ $notif['id'] }}">
            {{-- Avatar --}}
            <div class="notif-avatar-wrapper">
                @if($notif['user_avatar'])
                    <img src="{{ asset('storage/' . $notif['user_avatar']) }}" alt="{{ $notif['user_name'] }}" class="notif-avatar-img">
                @else
                    <div class="notif-avatar-placeholder" style="background: {{ ['#2D5A27','#1565c0','#e65100','#6a1b9a','#c62828','#00695c'][crc32($notif['user_name']) % 6] }}">
                        {{ $notif['user_initial'] }}
                    </div>
                @endif
                {{-- Unread dot --}}
                @if(!$notif['read'])
                <span class="notif-unread-dot"></span>
                @endif
            </div>

            {{-- Content --}}
            <div class="notif-content">
                <div class="notif-meta">
                    <span class="notif-user-name">{{ $notif['user_name'] }}</span>
                    @if($notif['order_id'])
                    <span class="notif-order-id">{{ $notif['order_id'] }}</span>
                    @endif
                </div>
                <p class="notif-message">{{ $notif['message'] }}</p>

                {{-- Action Buttons --}}
                @if(count($notif['actions']) > 0)
                <div class="notif-actions">
                    @foreach($notif['actions'] as $action)
                        @if($action['type'] === 'primary')
                            <form action="{{ $action['route'] }}" method="POST" class="notif-action-form">
                                @csrf
                                <button type="submit" class="notif-btn notif-btn-primary" onclick="return confirm('Konfirmasi tindakan ini?')">
                                    {{ $action['label'] }}
                                </button>
                            </form>
                        @else
                            <a href="{{ $action['route'] }}" class="notif-btn notif-btn-secondary">
                                {{ $action['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Timestamp --}}
            <div class="notif-time">
                <span class="notif-time-text">{{ $notif['time']->diffForHumans() }}</span>
            </div>
        </div>
        @empty
        <div class="notif-empty" id="notif-empty">
            <div class="notif-empty-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>Tidak Ada Notifikasi</h3>
            <p>Belum ada notifikasi untuk ditampilkan saat ini.</p>
        </div>
        @endforelse
    </div>

    {{-- LOAD MORE --}}
    @if($notifications->count() > 0)
    <div class="notifikasi-footer">
        <button class="btn-load-more" id="btn-load-more">
            Muat Notifikasi Lama <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    @endif
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Tab animation ──
    const tabs = document.querySelectorAll('.notif-tab');
    tabs.forEach(tab => {
        tab.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-1px)';
        });
        tab.addEventListener('mouseleave', function() {
            this.style.transform = '';
        });
    });

    // ── Notification item entrance animation ──
    const items = document.querySelectorAll('.notif-item');
    items.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(16px)';
        setTimeout(() => {
            item.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'translateY(0)';
        }, 80 * index);
    });

    // ── Load More button (show toast feedback) ──
    const loadMoreBtn = document.getElementById('btn-load-more');
    if (loadMoreBtn) {
        loadMoreBtn.addEventListener('click', function() {
            showToast('Semua notifikasi sudah ditampilkan.', false);
            this.innerHTML = '<i class="fas fa-check"></i> Semua Dimuat';
            this.disabled = true;
            this.classList.add('btn-load-more-done');
        });
    }

    // ── Toast helper ──
    function showToast(msg, isError) {
        const c = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = 'toast' + (isError ? ' toast-error' : '');
        t.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${msg}`;
        c.appendChild(t);
        setTimeout(() => {
            t.style.opacity = '0';
            t.style.transform = 'translateX(100%)';
            setTimeout(() => t.remove(), 300);
        }, 3500);
    }
});
</script>
@endsection
