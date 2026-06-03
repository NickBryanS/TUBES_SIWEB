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
        <div class="stat-card stat-card-revenue" id="stat-revenue">
            <div class="stat-icon stat-icon-revenue">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-info">
                <div class="stat-label-row">
                    <span class="stat-label" id="revenue-label">
                        PENDAPATAN {{ strtoupper($periodePendapatan === 'hari' ? 'HARI INI' : ($periodePendapatan === 'minggu' ? 'MINGGU INI' : 'BULAN INI')) }}
                    </span>
                    <div class="revenue-filter" id="revenue-filter">
                        <button class="revenue-filter-btn" id="revenue-filter-btn" type="button" aria-label="Pilih periode">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="revenue-dropdown" id="revenue-dropdown">
                            <button type="button" class="revenue-option {{ $periodePendapatan === 'hari' ? 'active' : '' }}" data-period="hari" data-value="{{ $pendapatanHari }}" data-label="PENDAPATAN HARI INI">
                                <i class="fas fa-calendar-day"></i> Hari Ini
                            </button>
                            <button type="button" class="revenue-option {{ $periodePendapatan === 'minggu' ? 'active' : '' }}" data-period="minggu" data-value="{{ $pendapatanMinggu }}" data-label="PENDAPATAN MINGGU INI">
                                <i class="fas fa-calendar-week"></i> Minggu Ini
                            </button>
                            <button type="button" class="revenue-option {{ $periodePendapatan === 'bulan' ? 'active' : '' }}" data-period="bulan" data-value="{{ $pendapatanBulan }}" data-label="PENDAPATAN BULAN INI">
                                <i class="fas fa-calendar-alt"></i> Bulan Ini
                            </button>
                        </div>
                    </div>
                </div>
                <div class="stat-value stat-value-currency" id="revenue-value">Rp {{ number_format($periodePendapatan === 'hari' ? $pendapatanHari : ($periodePendapatan === 'minggu' ? $pendapatanMinggu : $pendapatanBulan), 0, ',', '.') }}</div>
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

{{-- MODAL: DENDA KHUSUS --}}
<div class="modal-overlay" id="modal-denda">
    <div class="modal-box" style="max-width: 460px;">
        <div class="modal-box-header">
            <h3><i class="fas fa-exclamation-triangle" style="color:#d97706;"></i> Tetapkan Denda</h3>
            <button class="modal-close-btn" data-close="modal-denda">&times;</button>
        </div>
        <form id="form-denda">
            @csrf
            <input type="hidden" id="denda-trx-id">
            <div class="modal-box-body" style="padding:24px;">
                <p style="font-size:0.84rem;color:#6b7280;margin:0 0 18px;">Masukkan nominal denda untuk barang yang rusak atau hilang. Denda akan ditambahkan ke tagihan pelanggan.</p>
                <div style="margin-bottom:16px;">
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Nominal Denda (Rp) <span style="color:#ef4444;">*</span></label>
                    <input type="number" id="input-denda" name="denda" min="0" step="1000" placeholder="Contoh: 50000" required style="width:100%;padding:10px 14px;border:1px solid #e0e0e0;border-radius:8px;font-size:0.85rem;font-family:inherit;color:#111827;box-sizing:border-box;transition:all 0.2s;">
                    <span class="denda-error error-denda" style="display:block;font-size:0.72rem;color:#ef4444;margin-top:4px;"></span>
                </div>
                <div style="margin-bottom:0;">
                    <label style="display:block;font-size:0.82rem;font-weight:600;color:#374151;margin-bottom:6px;">Keterangan Denda <span style="color:#ef4444;">*</span></label>
                    <textarea id="input-keterangan-denda" name="keterangan_denda" rows="3" placeholder="Contoh: Tiang tenda patah, sleeping bag sobek" required style="width:100%;padding:10px 14px;border:1px solid #e0e0e0;border-radius:8px;font-size:0.85rem;font-family:inherit;color:#111827;box-sizing:border-box;resize:vertical;min-height:70px;transition:all 0.2s;"></textarea>
                    <span class="denda-error error-keterangan_denda" style="display:block;font-size:0.72rem;color:#ef4444;margin-top:4px;"></span>
                </div>
            </div>
            <div class="modal-box-footer" style="background:#fafaf8;border-radius:0 0 16px 16px;">
                <button type="button" class="btn btn-secondary" data-close="modal-denda">Batal</button>
                <button type="submit" class="btn" style="background:#d97706;color:#fff;" id="btn-submit-denda">
                    <i class="fas fa-save"></i> Simpan Denda
                </button>
            </div>
        </form>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    // ── Revenue Period Filter (instant switch, no reload) ──
    const revenueFilterBtn = document.getElementById('revenue-filter-btn');
    const revenueDropdown = document.getElementById('revenue-dropdown');
    const revenueLabel = document.getElementById('revenue-label');
    const revenueValue = document.getElementById('revenue-value');

    if (revenueFilterBtn && revenueDropdown) {
        // Toggle dropdown
        revenueFilterBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            revenueDropdown.classList.toggle('open');
            revenueFilterBtn.classList.toggle('open');
        });

        // Option selection
        document.querySelectorAll('.revenue-option').forEach(option => {
            option.addEventListener('click', function(e) {
                e.stopPropagation();
                const value = parseFloat(this.dataset.value) || 0;
                const label = this.dataset.label;

                // Update label
                revenueLabel.textContent = label;

                // Animate value change
                revenueValue.style.transform = 'translateY(-4px)';
                revenueValue.style.opacity = '0';
                setTimeout(() => {
                    revenueValue.textContent = 'Rp ' + value.toLocaleString('id-ID');
                    revenueValue.style.transform = 'translateY(4px)';
                    requestAnimationFrame(() => {
                        revenueValue.style.transition = 'all 0.25s ease';
                        revenueValue.style.transform = 'translateY(0)';
                        revenueValue.style.opacity = '1';
                    });
                }, 150);

                // Update active state
                document.querySelectorAll('.revenue-option').forEach(o => o.classList.remove('active'));
                this.classList.add('active');

                // Close dropdown
                revenueDropdown.classList.remove('open');
                revenueFilterBtn.classList.remove('open');
            });
        });

        // Close on outside click
        document.addEventListener('click', function() {
            revenueDropdown.classList.remove('open');
            revenueFilterBtn.classList.remove('open');
        });
    }

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

                    // ── Denda Section ──
                    const dendaVal = parseFloat(data.denda) || 0;
                    const dendaKet = data.keterangan_denda || '';
                    const isActive = !['dibatalkan', 'selesai'].includes(data.status_transaksi) || dendaVal > 0;

                    html += `
                    <div class="modal-section">
                        <div class="modal-section-header">
                            <h4><i class="fas fa-gavel"></i> Denda Khusus</h4>
                            ${!['dibatalkan'].includes(data.status_transaksi) ? `<button type="button" class="modal-link" id="btn-open-denda" data-id="${data.id}" data-denda="${dendaVal}" data-ket="${dendaKet.replace(/"/g, '&quot;')}" style="cursor:pointer;border:none;background:none;font-family:inherit;"><i class="fas fa-edit"></i> ${dendaVal > 0 ? 'Ubah' : 'Tetapkan'} Denda</button>` : ''}
                        </div>
                        ${dendaVal > 0 ? `
                            <div style="background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:14px 16px;">
                                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
                                    <span style="font-size:0.75rem;font-weight:700;color:#92400e;text-transform:uppercase;letter-spacing:0.05em;">NOMINAL DENDA</span>
                                    <span style="font-size:1.1rem;font-weight:800;color:#b45309;">${formatRp(dendaVal)}</span>
                                </div>
                                <div style="font-size:0.8rem;color:#78350f;line-height:1.5;"><strong>Keterangan:</strong> ${dendaKet || '-'}</div>
                            </div>
                        ` : `
                            <div style="text-align:center;padding:16px;color:#9ca3af;font-size:0.84rem;">
                                <i class="fas fa-check-circle" style="color:#10b981;margin-right:6px;"></i>Tidak ada denda untuk transaksi ini.
                            </div>
                        `}
                    </div>`;

                    // ── Pembatalan & Pengembalian Dana ──
                    if (data.status_transaksi === 'dibatalkan') {
                        html += `
                        <div class="modal-section">
                            <div class="modal-section-header">
                                <h4 style="color: #e74c3c;"><i class="fas fa-undo"></i> Pengembalian Dana (Refund)</h4>
                            </div>`;
                            
                        if (data.rekening_pengembalian) {
                            html += `
                            <div style="background: rgba(231,76,60,0.05); border: 1px solid rgba(231,76,60,0.2); border-radius: 10px; padding: 16px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px;">
                                    <div>
                                        <div style="font-size: 0.75rem; color: #c0392b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Bank / E-Wallet</div>
                                        <div style="font-size: 0.95rem; color: #333; font-weight: 600;">${data.bank_pengembalian || '-'}</div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.75rem; color: #c0392b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Atas Nama</div>
                                        <div style="font-size: 0.95rem; color: #333; font-weight: 600;">${data.atas_nama_pengembalian || '-'}</div>
                                    </div>
                                    <div style="grid-column: 1 / -1; margin-top: 4px;">
                                        <div style="font-size: 0.75rem; color: #c0392b; text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Nomor Rekening</div>
                                        <div style="font-family: monospace; font-size: 1.15rem; color: #e74c3c; background: #fff; padding: 10px 12px; border: 1px dashed #e74c3c; border-radius: 6px; letter-spacing: 0.5px;">${data.rekening_pengembalian}</div>
                                    </div>
                                </div>
                            </div>`;
                        } else {
                            html += `
                            <div style="text-align: center; padding: 20px; background: #f9fafb; border: 1px dashed #d1d5db; border-radius: 10px; color: #6b7280; font-size: 0.9rem;">
                                <i class="fas fa-info-circle" style="margin-right: 6px;"></i> Tidak memerlukan pengembalian dana (belum dibayar).
                            </div>`;
                        }
                        
                        html += `</div>`;
                    }

                    body.innerHTML = html;

                    // ── Bind denda button ──
                    const btnDenda = document.getElementById('btn-open-denda');
                    if (btnDenda) {
                        btnDenda.addEventListener('click', function() {
                            document.getElementById('denda-trx-id').value = this.dataset.id;
                            document.getElementById('input-denda').value = parseFloat(this.dataset.denda) || '';
                            document.getElementById('input-keterangan-denda').value = this.dataset.ket || '';
                            document.querySelectorAll('.denda-error').forEach(el => el.textContent = '');
                            openModal('modal-denda');
                        });
                    }

                    // ── Footer buttons ──
                    const isPending = ['menunggu', 'menunggu_admin'].includes(data.status_transaksi);

                    // Tombol Cetak Nota selalu di kiri
                    footerLeft.innerHTML = `
                        <a href="{{ url('admin/transaksi') }}/${data.id}/nota" target="_blank" class="btn btn-outline-dark"><i class="fas fa-print"></i> Cetak Nota</a>
                    `;

                    // Tombol di kanan: TOLAK (untuk pending), lalu KONFIRMASI LUNAS (jika belum lunas)
                    footerRight.innerHTML = '';

                    if (isPending) {
                        footerRight.innerHTML += `
                            <form action="{{ url('admin/transaksi') }}/${data.id}/reject" method="POST" onsubmit="return confirm('Tolak transaksi ini?')" style="display:inline;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-times"></i> TOLAK</button>
                            </form>
                        `;
                    }

                    // Konfirmasi Lunas / Sudah Bayar button (kontekstual)
                    if (data.payment && data.payment.status_pembayaran !== 'terverifikasi' && !['dibatalkan'].includes(data.status_transaksi)) {
                        const hasBukti = data.payment.bukti_pembayaran;
                        const btnLabel = hasBukti ? 'KONFIRMASI LUNAS' : 'SUDAH BAYAR';
                        const btnIcon = hasBukti ? 'fa-check-double' : 'fa-money-bill-wave';
                        const confirmMsg = hasBukti
                            ? 'Konfirmasi pembayaran lunas berdasarkan bukti transfer yang diunggah?'
                            : 'Konfirmasi bahwa pelanggan sudah membayar langsung (cash) di toko?';

                        footerRight.innerHTML += `
                            <form action="{{ url('admin/transaksi') }}/${data.id}/lunas" method="POST" onsubmit="return confirm('${confirmMsg}')" style="display:inline;">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-success"><i class="fas ${btnIcon}"></i> ${btnLabel}</button>
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

    // ── Submit Denda Form ──
    document.getElementById('form-denda').addEventListener('submit', function(e) {
        e.preventDefault();
        document.querySelectorAll('.denda-error').forEach(el => el.textContent = '');

        const trxId = document.getElementById('denda-trx-id').value;
        const body = {
            denda: document.getElementById('input-denda').value,
            keterangan_denda: document.getElementById('input-keterangan-denda').value,
        };

        const csrfToken = document.querySelector('input[name="_token"]')?.value || '{{ csrf_token() }}';

        fetch(`{{ url('admin/transaksi') }}/${trxId}/denda`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-denda');
                closeModal('modal-detail-trx');
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else if (data.errors) {
                for (const field in data.errors) {
                    const el = document.querySelector(`.error-${field}`);
                    if (el) el.textContent = data.errors[field][0];
                }
            } else {
                showToast(data.message || 'Terjadi kesalahan', true);
            }
        })
        .catch(() => showToast('Terjadi kesalahan', true));
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
