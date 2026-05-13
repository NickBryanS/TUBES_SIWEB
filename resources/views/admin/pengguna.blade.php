@extends('admin.layouts.admin')

@section('title', 'Manajemen Pengguna - Garkadala Admin')
@section('sidebar-pengguna', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/pengguna.css') }}">
@endsection

@section('content')
<div class="pengguna-page">
    {{-- HEADER --}}
    <div class="pengguna-header">
        <div>
            <h1 class="pengguna-title">Manajemen Pengguna</h1>
            <p class="pengguna-subtitle">Pantau aktivitas pengguna Gardakala, verifikasi identitas, dan kelola status akses dalam satu dashboard terintegrasi.</p>
        </div>
        <div class="header-actions">
            <button class="btn-tambah-pengguna" id="btn-tambah-pengguna">
                <i class="fas fa-plus"></i> Tambah Pengguna
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="pengguna-stats">
        <div class="stat-card">
            <div class="stat-icon stat-icon-total">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">TOTAL PENGGUNA</div>
                <div class="stat-value">{{ number_format($totalPengguna) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-verified">
                <i class="fas fa-user-check"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">TERVERIFIKASI</div>
                <div class="stat-value">{{ number_format($terverifikasi) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-blocked">
                <i class="fas fa-user-slash"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">DIBLOKIR</div>
                <div class="stat-value">{{ number_format($diblokir) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-active">
                <i class="fas fa-signal"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">AKTIF HARI INI</div>
                <div class="stat-value">{{ number_format($aktifHariIni) }}</div>
            </div>
        </div>
    </div>

    {{-- TABLE CONTENT --}}
    <div class="pengguna-content">
        {{-- Tabs & Filter --}}
        <div class="pengguna-toolbar">
            <div class="toolbar-tabs">
                <a href="{{ route('admin.pengguna.index', array_merge(request()->except('tab', 'page'), ['tab' => 'semua'])) }}"
                   class="tab-item {{ $tab === 'semua' ? 'active' : '' }}">Semua</a>
                <a href="{{ route('admin.pengguna.index', array_merge(request()->except('tab', 'page'), ['tab' => 'aktif'])) }}"
                   class="tab-item {{ $tab === 'aktif' ? 'active' : '' }}">Aktif</a>
                <a href="{{ route('admin.pengguna.index', array_merge(request()->except('tab', 'page'), ['tab' => 'diblokir'])) }}"
                   class="tab-item {{ $tab === 'diblokir' ? 'active' : '' }}">Diblokir</a>
            </div>
            <div class="toolbar-actions">
                <button class="btn-filter-lanjutan" id="btn-filter-lanjutan">
                    <i class="fas fa-sliders-h"></i> Filter Lanjutan
                </button>
            </div>
        </div>

        {{-- TABLE --}}
        <div class="pengguna-table-wrapper">
            <table class="pengguna-table">
                <thead>
                    <tr>
                        <th>PENGGUNA</th>
                        <th>KONTAK</th>
                        <th>TANGGAL DAFTAR</th>
                        <th>JUMLAH TRANSAKSI</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $colors = ['#2D5A27','#1565c0','#e65100','#6a1b9a','#c62828','#00695c','#4527a0','#ad1457'];
                        $avatarColor = $colors[$user->id % count($colors)];
                        $initials = strtoupper(collect(explode(' ', $user->nama_lengkap))->map(fn($w) => substr($w, 0, 1))->take(2)->join(''));
                    @endphp
                    <tr class="pengguna-row" data-id="{{ $user->id }}">
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar" style="background: {{ $avatarColor }}">
                                    {{ $initials }}
                                </div>
                                <div class="user-info">
                                    <span class="user-name">{{ $user->nama_lengkap }}</span>
                                    <span class="user-meta">
                                        <i class="fas fa-id-card"></i> ID: GRK-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-cell">
                                <span class="contact-email">{{ $user->email }}</span>
                                <span class="contact-phone">{{ $user->nomor_telepon ?? '-' }}</span>
                            </div>
                        </td>
                        <td>
                            <span class="date-cell">
                                {{ $user->created_at->format('d M') }}<br>
                                <small>{{ $user->created_at->format('Y') }}</small>
                            </span>
                        </td>
                        <td>
                            <span class="transaction-count">{{ $user->transactions_count }} Transaksi</span>
                        </td>
                        <td>
                            @if($user->status_akun === 'aktif')
                                <span class="status-badge status-aktif">
                                    <span class="status-dot"></span>
                                    AKTIF
                                </span>
                            @elseif($user->status_akun === 'banned')
                                <span class="status-badge status-diblokir">
                                    <span class="status-dot"></span>
                                    DIBLOKIR
                                </span>
                            @else
                                <span class="status-badge status-nonaktif">
                                    <span class="status-dot"></span>
                                    NONAKTIF
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view-user" data-id="{{ $user->id }}" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form action="{{ route('admin.pengguna.toggle-status', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('{{ $user->status_akun === 'aktif' ? 'Blokir' : 'Aktifkan' }} pengguna ini?')">
                                    @csrf
                                    <button type="submit" class="btn-action {{ $user->status_akun === 'aktif' ? 'btn-block' : 'btn-unblock' }}" title="{{ $user->status_akun === 'aktif' ? 'Blokir' : 'Aktifkan' }}">
                                        <i class="fas {{ $user->status_akun === 'aktif' ? 'fa-ban' : 'fa-check-circle' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.pengguna.destroy', $user->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus pengguna ini secara permanen?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <i class="fas fa-users-slash"></i>
                                <p>Belum ada pengguna terdaftar</p>
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
                Menampilkan {{ $users->firstItem() ?? 0 }} dari {{ $users->total() }} pengguna
            </span>
            @if($users->hasPages())
            <nav>
                <ul class="pagination">
                    @if ($users->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $users->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($users->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $users->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{--  MODAL: DETAIL PENGGUNA                                       --}}
{{-- ============================================================ --}}
<div class="modal-overlay" id="modal-detail-user">
    <div class="modal-box modal-box-lg">
        {{-- Header --}}
        <div class="modal-box-header">
            <div>
                <h3><i class="fas fa-user-circle"></i> Detail Pengguna</h3>
                <span class="modal-user-id" id="modal-user-id"></span>
            </div>
            <div class="modal-header-actions">
                <span class="modal-status-badge" id="modal-user-status"></span>
                <button class="modal-close-btn" data-close="modal-detail-user">&times;</button>
            </div>
        </div>

        {{-- Body --}}
        <div class="modal-box-body" id="modal-user-body">
            <div class="modal-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat detail pengguna...</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-box-footer" id="modal-user-footer" style="display:none;">
            <div class="modal-footer-left" id="modal-footer-left"></div>
            <div class="modal-footer-right" id="modal-footer-right"></div>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>

{{-- FAB --}}
<button class="fab-button" id="fab-tambah" title="Tambah Pengguna">
    <i class="fas fa-plus"></i>
</button>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Helpers ──
    function openModal(id) {
        const m = document.getElementById(id);
        m.style.display = 'flex';
        requestAnimationFrame(() => m.classList.add('active'));
    }
    function closeModal(id) {
        const m = document.getElementById(id);
        m.classList.remove('active');
        setTimeout(() => m.style.display = 'none', 300);
    }
    function showToast(msg, isError) {
        const c = document.getElementById('toast-container');
        const t = document.createElement('div');
        t.className = 'toast' + (isError ? ' toast-error' : '');
        t.innerHTML = `<i class="fas ${isError ? 'fa-exclamation-circle' : 'fa-check-circle'}"></i> ${msg}`;
        c.appendChild(t);
        setTimeout(() => { t.style.opacity = '0'; setTimeout(() => t.remove(), 300); }, 3500);
    }

    // ── Close Modal Buttons ──
    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.close));
    });
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });

    // ── Open Detail Modal ──
    document.querySelectorAll('.btn-view-user').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const body = document.getElementById('modal-user-body');
            const footer = document.getElementById('modal-user-footer');
            const footerLeft = document.getElementById('modal-footer-left');
            const footerRight = document.getElementById('modal-footer-right');

            body.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i><p>Memuat detail pengguna...</p></div>';
            footer.style.display = 'none';
            document.getElementById('modal-user-id').textContent = '';
            document.getElementById('modal-user-status').textContent = '';

            openModal('modal-detail-user');

            fetch(`{{ url('admin/pengguna') }}/${id}`)
                .then(r => r.json())
                .then(data => {
                    // Header
                    document.getElementById('modal-user-id').textContent = 'ID: GRK-' + String(data.id).padStart(4, '0');

                    const badge = document.getElementById('modal-user-status');
                    if (data.status_akun === 'aktif') {
                        badge.textContent = 'AKTIF';
                        badge.className = 'modal-status-badge status-badge status-aktif';
                    } else if (data.status_akun === 'banned') {
                        badge.textContent = 'DIBLOKIR';
                        badge.className = 'modal-status-badge status-badge status-diblokir';
                    } else {
                        badge.textContent = 'NONAKTIF';
                        badge.className = 'modal-status-badge status-badge status-nonaktif';
                    }

                    // Build body
                    let html = '';

                    // ── Informasi Dasar ──
                    const initials = data.nama_lengkap.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
                    html += `
                    <div class="modal-section">
                        <div class="modal-user-profile">
                            <div class="modal-user-avatar" style="background: #2D5A27">
                                ${initials}
                            </div>
                            <div class="modal-user-profile-info">
                                <span class="modal-user-name">${data.nama_lengkap}</span>
                                <span class="modal-user-role">${data.peran.toUpperCase()}</span>
                            </div>
                        </div>
                    </div>`;

                    // ── Detail Informasi ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-info-circle"></i> Informasi Pengguna</h4>
                        </div>
                        <div class="modal-info-grid">
                            <div class="modal-info-item">
                                <span class="modal-info-label">EMAIL</span>
                                <span class="modal-info-value">${data.email}</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">NO. TELEPON</span>
                                <span class="modal-info-value">${data.nomor_telepon || '-'}</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">TANGGAL DAFTAR</span>
                                <span class="modal-info-value">${data.created_at}</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">TERAKHIR AKTIF</span>
                                <span class="modal-info-value">${data.updated_at}</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">VERIFIKASI</span>
                                <span class="modal-info-value">
                                    ${data.status_verifikasi
                                        ? '<span class="verify-badge verified"><i class="fas fa-check-circle"></i> Terverifikasi</span>'
                                        : '<span class="verify-badge not-verified"><i class="fas fa-times-circle"></i> Belum Verifikasi</span>'
                                    }
                                </span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">TOTAL TRANSAKSI</span>
                                <span class="modal-info-value">${data.transactions_count} Transaksi</span>
                            </div>
                        </div>
                    </div>`;

                    // ── Dokumen Identitas ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-id-card"></i> Dokumen Identitas (KTP)</h4>
                        </div>
                        <div class="modal-ktp-wrapper">
                            ${data.dokumen_identitas
                                ? `<img src="${data.dokumen_identitas}" alt="Dokumen Identitas" class="modal-ktp-image">`
                                : `<div class="modal-ktp-placeholder"><i class="fas fa-id-card"></i><p>Dokumen identitas belum diunggah</p></div>`
                            }
                        </div>
                    </div>`;

                    body.innerHTML = html;

                    // ── Footer ──
                    footerLeft.innerHTML = `
                        <form action="{{ url('admin/pengguna') }}/${data.id}/verifikasi" method="POST" style="display:inline;" onsubmit="return confirm('${data.status_verifikasi ? 'Cabut verifikasi' : 'Verifikasi'} pengguna ini?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn ${data.status_verifikasi ? 'btn-outline-warning' : 'btn-outline-success'}">
                                <i class="fas ${data.status_verifikasi ? 'fa-times' : 'fa-check'}"></i>
                                ${data.status_verifikasi ? 'Cabut Verifikasi' : 'Verifikasi'}
                            </button>
                        </form>
                    `;
                    footerRight.innerHTML = `
                        <form action="{{ url('admin/pengguna') }}/${data.id}/toggle-status" method="POST" style="display:inline;" onsubmit="return confirm('${data.status_akun === 'aktif' ? 'Blokir' : 'Aktifkan'} pengguna ini?')">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit" class="btn ${data.status_akun === 'aktif' ? 'btn-outline-danger' : 'btn-primary'}">
                                <i class="fas ${data.status_akun === 'aktif' ? 'fa-ban' : 'fa-check-circle'}"></i>
                                ${data.status_akun === 'aktif' ? 'BLOKIR' : 'AKTIFKAN'}
                            </button>
                        </form>
                    `;
                    footer.style.display = 'flex';
                })
                .catch(err => {
                    console.error(err);
                    body.innerHTML = '<div class="modal-loading" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data pengguna</p></div>';
                });
        });
    });

    // ── Keyboard ──
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
});
</script>
@endsection
