@extends('admin.layouts.admin')

@section('title', 'Moderasi Ulasan - Garkadala Admin')
@section('sidebar-ulasan', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/ulasan.css') }}">
@endsection

@section('content')
<div class="ulasan-page">

    {{-- HEADER --}}
    <div class="ulasan-header">
        <div>
            <h1 class="ulasan-title">Moderasi Ulasan</h1>
            <p class="ulasan-subtitle">Pantau dan hapus ulasan yang mengandung konten spam atau tidak pantas.</p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="ulasan-stats">
        <div class="ul-stat-card ul-stat-primary">
            <div class="ul-stat-label">TOTAL ULASAN</div>
            <div class="ul-stat-value">{{ $totalUlasan }}<span class="ul-stat-unit">Ulasan</span></div>
        </div>
        <div class="ul-stat-card ul-stat-success">
            <div class="ul-stat-label">ULASAN POSITIF</div>
            <div class="ul-stat-value">{{ $ulasanPositif }}<span class="ul-stat-unit">Ulasan</span></div>
        </div>
        <div class="ul-stat-card ul-stat-danger">
            <div class="ul-stat-label">ULASAN NEGATIF</div>
            <div class="ul-stat-value">{{ $ulasanNegatif }}<span class="ul-stat-unit">Ulasan</span></div>
        </div>
        <div class="ul-stat-card ul-stat-warning">
            <div class="ul-stat-label">RATA-RATA RATING</div>
            <div class="ul-stat-value">{{ $rataRating }}<span class="ul-stat-unit">/ 5</span></div>
        </div>
    </div>

    {{-- DAFTAR ULASAN --}}
    <div class="ulasan-content">
        <div class="ulasan-toolbar">
            <h2 class="ulasan-subtitle-2">Daftar Ulasan</h2>
            <form method="GET" action="{{ route('admin.ulasan.index') }}" class="toolbar-filters-row" id="filter-form">
                <div class="ul-search-wrap">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="ul-search-input"
                        placeholder="Cari ulasan, pengguna, atau produk..."
                        value="{{ request('search') }}">
                </div>
                <select name="rating" class="ul-filter-select" onchange="document.getElementById('filter-form').submit()">
                    <option value="">Semua Rating</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                            {{ $i }} Bintang
                        </option>
                    @endfor
                </select>
                <span class="toolbar-info">{{ $reviews->total() }} ulasan ditemukan</span>
            </form>
        </div>

        {{-- TABEL --}}
        <div class="ulasan-table-wrapper">
            <table class="ulasan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Pengguna</th>
                        <th>Produk</th>
                        <th>Rating</th>
                        <th>Isi Ulasan</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $index => $review)
                    @php
                        $ratingClass = 'rating-' . $review->rating;
                        $initial = strtoupper(substr($review->user->nama_lengkap ?? 'U', 0, 1));
                    @endphp
                    <tr>
                        <td>{{ $reviews->firstItem() + $index }}</td>
                        <td>
                            <div class="user-info">
                                <div class="user-avatar">{{ $initial }}</div>
                                <div>
                                    <div class="user-name">{{ $review->user->nama_lengkap ?? '-' }}</div>
                                    <div class="user-email">{{ $review->user->email ?? '-' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="product-name-cell">
                                <i class="fas fa-box"></i>{{ $review->product->nama_produk ?? '-' }}
                            </span>
                        </td>
                        <td>
                            <div class="rating-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star star {{ $i <= $review->rating ? 'star-filled' : 'star-empty' }}"></i>
                                @endfor
                                <span class="rating-num">{{ $review->rating }}/5</span>
                            </div>
                        </td>
                        <td>
                            @if($review->ulasan)
                                <span class="ulasan-text" title="{{ $review->ulasan }}">{{ $review->ulasan }}</span>
                            @else
                                <span class="ulasan-empty">Tidak ada komentar</span>
                            @endif
                        </td>
                        <td>
                            <span class="date-text">{{ $review->created_at->format('d M Y') }}</span>
                        </td>
                        <td>
                            <button class="btn-action-delete"
                                data-id="{{ $review->id }}"
                                data-preview="{{ Str::limit($review->ulasan ?? 'Tidak ada komentar.', 80) }}"
                                title="Hapus Ulasan">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-star-half-alt"></i>
                                <p>Belum ada ulasan dari pengguna</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="pagination-wrapper">
            <span class="pagination-info">
                Menampilkan {{ $reviews->firstItem() ?? 0 }}-{{ $reviews->lastItem() ?? 0 }} dari {{ $reviews->total() }} ulasan
            </span>
            @if($reviews->hasPages())
            <nav>
                <ul class="pagination">
                    @if($reviews->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $reviews->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach($reviews->getUrlRange(1, $reviews->lastPage()) as $page => $url)
                        @if($page == $reviews->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if($reviews->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $reviews->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="ul-modal-overlay" id="modal-delete">
    <div class="ul-modal-box">
        <div class="ul-modal-header">
            <h3>Hapus Ulasan?</h3>
            <button class="ul-modal-close" id="modal-close-btn">&times;</button>
        </div>
        <div class="ul-modal-body">
            <div class="ul-delete-icon"><i class="fas fa-trash-alt"></i></div>
            <h4>Hapus Ulasan Ini?</h4>
            <p>Ulasan yang dihapus tidak dapat dikembalikan.</p>
            <span class="ul-review-preview" id="delete-preview-text"></span>
        </div>
        <div class="ul-modal-footer">
            <button type="button" class="ul-btn ul-btn-secondary" id="btn-batal">Batal</button>
            <button type="button" class="ul-btn ul-btn-danger" id="btn-confirm-delete">
                <i class="fas fa-trash"></i> Hapus Ulasan
            </button>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
        || document.querySelector('input[name="_token"]')?.value
        || '{{ csrf_token() }}';

    let deleteId = null;

    // ── Modal helpers ──
    function openModal() {
        const m = document.getElementById('modal-delete');
        m.style.display = 'flex';
        requestAnimationFrame(() => m.classList.add('active'));
    }
    function closeModal() {
        const m = document.getElementById('modal-delete');
        m.classList.remove('active');
        setTimeout(() => m.style.display = 'none', 300);
    }
    function showToast(msg, isError = false) {
        const c = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = 'toast' + (isError ? ' toast-error' : '');
        t.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${msg}`;
        c.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3500);
    }

    // ── Buka modal hapus ──
    document.querySelectorAll('.btn-action-delete').forEach(btn => {
        btn.addEventListener('click', function () {
            deleteId = this.dataset.id;
            const preview = this.dataset.preview || 'Tidak ada komentar.';
            document.getElementById('delete-preview-text').textContent = '"' + preview + '"';
            openModal();
        });
    });

    // ── Tutup modal ──
    document.getElementById('btn-batal').addEventListener('click', closeModal);
    document.getElementById('modal-close-btn').addEventListener('click', closeModal);
    document.getElementById('modal-delete').addEventListener('click', function (e) {
        if (e.target === this) closeModal();
    });

    // ── Konfirmasi hapus ──
    document.getElementById('btn-confirm-delete').addEventListener('click', function () {
        if (!deleteId) return;

        fetch(`{{ url('admin/ulasan') }}/${deleteId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(r => r.json())
        .then(data => {
            closeModal();
            if (data.success) {
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Terjadi kesalahan', true);
            }
        })
        .catch(() => {
            closeModal();
            showToast('Terjadi kesalahan, coba lagi.', true);
        });
    });

    // ── Keyboard Escape ──
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });

    // ── Auto-submit search on Enter ──
    document.querySelector('.ul-search-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') document.getElementById('filter-form').submit();
    });
});
</script>
@endsection
