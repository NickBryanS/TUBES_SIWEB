@extends('admin.layouts.admin')

@section('title', 'Manajemen Transaksi - Garkadala Admin')
@section('sidebar-transaksi', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/transaksi.css') }}">
@endsection

@section('content')
<div class="transaksi-page">
    {{-- HEADER --}}
    <div class="transaksi-header">
        <div>
            <h1 class="transaksi-title">Manajemen Transaksi</h1>
            <p class="transaksi-subtitle">Kelola penyewaan, verifikasi pembayaran, dan validasi dokumen.</p>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="transaksi-stats">
        <div class="stat-card">
            <div class="stat-icon stat-icon-total">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">TOTAL TRANSAKSI</div>
                <div class="stat-value">{{ number_format($totalTransaksi) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">MENUNGGU VERIFIKASI</div>
                <div class="stat-value">{{ number_format($menungguVerifikasi) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-active">
                <i class="fas fa-spinner"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">SEDANG BERJALAN</div>
                <div class="stat-value">{{ number_format($sedangBerjalan) }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-icon-revenue">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label">PENDAPATAN BULAN INI</div>
                <div class="stat-value stat-value-currency">Rp {{ number_format($pendapatanBulan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- TABLE CONTENT --}}
    <div class="transaksi-content">
        <div class="transaksi-toolbar">
            <h2 class="transaksi-subtitle-2">Daftar Transaksi</h2>
            <div class="toolbar-actions">
                <form method="GET" action="{{ route('admin.transaksi.index') }}" class="toolbar-filters" id="filter-form-transaksi">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <div class="filter-group">
                        <select name="status" class="filter-select" onchange="document.getElementById('filter-form-transaksi').submit()">
                            <option value="">Semua Status</option>
                            <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                            <option value="menunggu_admin" {{ request('status') == 'menunggu_admin' ? 'selected' : '' }}>Menunggu Admin</option>
                            <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                            <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="transaksi-table-wrapper">
            <table class="transaksi-table">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Sewa</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trx)
                    @php
                        $statusMap = [
                            'menunggu'       => ['label' => 'Menunggu',        'class' => 'status-menunggu'],
                            'menunggu_admin' => ['label' => 'Menunggu Admin',  'class' => 'status-menunggu'],
                            'diproses'       => ['label' => 'Diproses',        'class' => 'status-diproses'],
                            'dikirim'        => ['label' => 'Dikirim',         'class' => 'status-dikirim'],
                            'selesai'        => ['label' => 'Selesai',         'class' => 'status-selesai'],
                            'dibatalkan'     => ['label' => 'Dibatalkan',      'class' => 'status-dibatalkan'],
                        ];
                        $st = $statusMap[$trx->status_transaksi] ?? ['label' => $trx->status_transaksi, 'class' => ''];

                        $payStatusMap = [
                            'menunggu'              => ['label' => 'Belum Bayar',         'class' => 'pay-menunggu'],
                            'menunggu_verifikasi'   => ['label' => 'Menunggu Verifikasi',  'class' => 'pay-menunggu'],
                            'pending'               => ['label' => 'Pending',              'class' => 'pay-menunggu'],
                            'terverifikasi'         => ['label' => 'Lunas',                'class' => 'pay-lunas'],
                            'ditolak'               => ['label' => 'Ditolak',              'class' => 'pay-ditolak'],
                        ];
                        $payStatus = $trx->payment
                            ? ($payStatusMap[$trx->payment->status_pembayaran] ?? ['label' => $trx->payment->status_pembayaran, 'class' => ''])
                            : ['label' => 'Belum Bayar', 'class' => 'pay-menunggu'];
                    @endphp
                    <tr class="transaksi-row" data-id="{{ $trx->id }}">
                        <td>
                            <span class="order-id">#WB-{{ str_pad($trx->id, 8, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="customer-avatar" style="background: {{ ['#2D5A27','#1565c0','#e65100','#6a1b9a','#c62828','#00695c'][$trx->id % 6] }}">
                                    {{ strtoupper(substr($trx->user->nama_lengkap ?? 'U', 0, 2)) }}
                                </div>
                                <div class="customer-info">
                                    <span class="customer-name">{{ $trx->user->nama_lengkap ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="date-range">{{ $trx->tanggal_mulai->format('d M') }} - {{ $trx->tanggal_selesai->format('d M') }}<br><small>{{ $trx->tanggal_mulai->format('Y') }}</small></span>
                        </td>
                        <td>
                            <span class="price-cell">Rp {{ number_format($trx->total_biaya, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <span class="status-badge {{ $st['class'] }}">
                                <span class="status-dot"></span>
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="pay-badge {{ $payStatus['class'] }}">{{ $payStatus['label'] }}</span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view-trx" data-id="{{ $trx->id }}" title="Detail & Verifikasi">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if(in_array($trx->status_transaksi, ['menunggu', 'menunggu_admin']))
                                <form action="{{ route('admin.transaksi.approve', $trx->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Setujui transaksi ini?')">
                                    @csrf
                                    <button type="submit" class="btn-action btn-approve" title="Setujui">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <form action="{{ route('admin.transaksi.reject', $trx->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Tolak transaksi ini?')">
                                    @csrf
                                    <button type="submit" class="btn-action btn-reject" title="Tolak">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada transaksi</p>
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
                Menampilkan {{ $transactions->firstItem() ?? 0 }} dari {{ $transactions->total() }} transaksi
            </span>
            @if($transactions->hasPages())
            <nav>
                <ul class="pagination">
                    @if ($transactions->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $transactions->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($transactions->getUrlRange(1, $transactions->lastPage()) as $page => $url)
                        @if ($page == $transactions->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($transactions->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $transactions->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
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
{{--  MODAL: DETAIL / VERIFIKASI TRANSAKSI                        --}}
{{-- ============================================================ --}}
<div class="modal-overlay" id="modal-detail-trx">
    <div class="modal-box modal-box-lg">
        {{-- Header --}}
        <div class="modal-box-header">
            <div>
                <h3><i class="fas fa-clipboard-check"></i> Verifikasi Transaksi</h3>
                <span class="modal-order-id" id="modal-trx-id"></span>
            </div>
            <div class="modal-header-actions">
                <span class="modal-status-badge" id="modal-status-badge"></span>
                <button class="modal-close-btn" data-close="modal-detail-trx">&times;</button>
            </div>
        </div>

        {{-- Body - scrollable --}}
        <div class="modal-box-body" id="modal-trx-body">
            <div class="modal-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat detail transaksi...</p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="modal-box-footer" id="modal-trx-footer" style="display:none;">
            <div class="modal-footer-left" id="modal-footer-left"></div>
            <div class="modal-footer-right" id="modal-footer-right"></div>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>

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
    function formatRp(n) {
        return 'Rp ' + Number(n).toLocaleString('id-ID');
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

    // ── Status helpers ──
    const statusLabels = {
        'menunggu': 'Menunggu',
        'menunggu_admin': 'Menunggu Admin',
        'diproses': 'Diproses',
        'dikirim': 'Dikirim',
        'selesai': 'Selesai',
        'dibatalkan': 'Dibatalkan',
    };
    const statusClasses = {
        'menunggu': 'status-menunggu',
        'menunggu_admin': 'status-menunggu',
        'diproses': 'status-diproses',
        'dikirim': 'status-dikirim',
        'selesai': 'status-selesai',
        'dibatalkan': 'status-dibatalkan',
    };

    // ── Open Detail Modal ──
    document.querySelectorAll('.btn-view-trx').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const body = document.getElementById('modal-trx-body');
            const footer = document.getElementById('modal-trx-footer');
            const footerLeft = document.getElementById('modal-footer-left');
            const footerRight = document.getElementById('modal-footer-right');

            body.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i><p>Memuat detail transaksi...</p></div>';
            footer.style.display = 'none';
            document.getElementById('modal-trx-id').textContent = '';
            document.getElementById('modal-status-badge').textContent = '';

            openModal('modal-detail-trx');

            fetch(`{{ url('admin/transaksi') }}/${id}`)
                .then(r => r.json())
                .then(data => {
                    // Header info
                    document.getElementById('modal-trx-id').textContent = '#WB-' + String(data.id).padStart(8, '0');

                    const badge = document.getElementById('modal-status-badge');
                    badge.textContent = statusLabels[data.status_transaksi] || data.status_transaksi;
                    badge.className = 'modal-status-badge status-badge ' + (statusClasses[data.status_transaksi] || '');

                    // Build body
                    let html = '';

                    // ── Informasi Pelanggan ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-user"></i> Informasi Pelanggan</h4>
                            <a href="#" class="modal-link">Lihat Profil</a>
                        </div>
                        <div class="modal-info-grid">
                            <div class="modal-info-item">
                                <span class="modal-info-label">NAMA LENGKAP</span>
                                <span class="modal-info-value">${data.user.nama_lengkap}</span>
                            </div>
                            <div class="modal-info-item">
                                <span class="modal-info-label">NO. WHATSAPP</span>
                                <span class="modal-info-value">${data.user.nomor_telepon || '-'}</span>
                            </div>
                        </div>
                    </div>`;

                    // ── Verifikasi Identitas (KTP) ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-id-card"></i> Verifikasi Identitas (KTP)</h4>
                        </div>
                        <div class="modal-ktp-wrapper">
                            ${data.foto_ktp
                                ? `<img src="${data.foto_ktp}" alt="Foto KTP" class="modal-ktp-image">`
                                : `<div class="modal-ktp-placeholder"><i class="fas fa-id-card"></i><p>Foto KTP belum diunggah</p></div>`
                            }
                        </div>
                    </div>`;

                    // ── Detail Penyewaan ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-box-open"></i> Detail Penyewaan</h4>
                        </div>
                        <div class="modal-items-list">`;

                    data.details.forEach(item => {
                        html += `
                        <div class="modal-item">
                            <div class="modal-item-icon">
                                ${item.url_gambar
                                    ? `<img src="${item.url_gambar}" alt="${item.nama_produk}">`
                                    : `<i class="fas fa-campground"></i>`
                                }
                            </div>
                            <div class="modal-item-info">
                                <span class="modal-item-name">${item.nama_produk}</span>
                                <span class="modal-item-meta">${item.jumlah} Unit · ${item.hari} Hari</span>
                            </div>
                            <span class="modal-item-price">${formatRp(item.subtotal)}</span>
                        </div>`;
                    });

                    html += `</div>`;

                    // Metode pengiriman
                    const metodeLabel = data.metode_pengambilan === 'pickup' ? 'Ambil di Toko (Pickup)' : 'Antar Jemput (Delivery)';
                    const metodeIcon = data.metode_pengambilan === 'pickup' ? 'fa-store' : 'fa-truck';
                    html += `
                        <div class="modal-delivery-row">
                            <div class="modal-delivery-label">
                                <i class="fas fa-shipping-fast"></i> Metode Pengiriman:
                            </div>
                            <div class="modal-delivery-value">
                                <i class="fas ${metodeIcon}"></i> ${metodeLabel}
                            </div>
                        </div>
                    </div>`;

                    // ── Bukti Pembayaran ──
                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-credit-card"></i> Bukti Pembayaran${data.payment ? ' (' + data.payment.metode_pembayaran.replace('_', ' ').toUpperCase() + ')' : ''}</h4>
                        </div>
                        <div class="modal-ktp-wrapper">
                            ${data.payment && data.payment.bukti_pembayaran
                                ? `<img src="${data.payment.bukti_pembayaran}" alt="Bukti Pembayaran" class="modal-ktp-image">`
                                : `<div class="modal-ktp-placeholder"><i class="fas fa-file-invoice-dollar"></i><p>Bukti pembayaran belum diunggah</p></div>`
                            }
                        </div>
                    </div>`;

                    body.innerHTML = html;

                    // ── Footer buttons ──
                    const isPending = ['menunggu', 'menunggu_admin'].includes(data.status_transaksi);

                    if (isPending) {
                        footerLeft.innerHTML = `
                            <form action="{{ url('admin/transaksi') }}/${data.id}/reject" method="POST" onsubmit="return confirm('Tolak transaksi ini?')" style="display:inline;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-times"></i> TOLAK</button>
                            </form>
                        `;
                        footerRight.innerHTML = `
                            <form action="{{ url('admin/transaksi') }}/${data.id}/approve" method="POST" onsubmit="return confirm('Validasi transaksi ini?')" style="display:inline;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> VALIDASI</button>
                            </form>
                        `;
                    } else {
                        footerLeft.innerHTML = '';
                        footerRight.innerHTML = '';
                    }

                    // Buat tombol Cetak Nota & Buat Perjanjian
                    let extraButtons = `
                        <a href="{{ url('pesanan') }}/${data.id}/nota" target="_blank" class="btn btn-outline-dark"><i class="fas fa-print"></i> Cetak Nota</a>
                    `;
                    if (!isPending) {
                        footerLeft.innerHTML = extraButtons;
                    } else {
                        footerLeft.innerHTML += extraButtons;
                    }

                    // Konfirmasi Lunas button
                    if (data.payment && data.payment.status_pembayaran !== 'terverifikasi' && !['dibatalkan'].includes(data.status_transaksi)) {
                        footerRight.innerHTML += `
                            <form action="{{ url('admin/transaksi') }}/${data.id}/lunas" method="POST" onsubmit="return confirm('Konfirmasi pembayaran lunas?')" style="display:inline;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-success"><i class="fas fa-check-double"></i> KONFIRMASI LUNAS</button>
                            </form>
                        `;
                    }

                    footer.style.display = 'flex';
                })
                .catch(err => {
                    console.error(err);
                    body.innerHTML = '<div class="modal-loading" style="color:#ef4444;"><i class="fas fa-exclamation-circle"></i><p>Gagal memuat data transaksi</p></div>';
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
