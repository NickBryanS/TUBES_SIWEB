@extends('admin.layouts.admin')

@section('title', 'Manajemen Pengiriman - Garkadala Admin')
@section('sidebar-pengiriman', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/pengiriman.css') }}">
@endsection

@section('content')
<div class="pengiriman-page">
    {{-- STAT CARDS --}}
    <div class="pengiriman-stats">
        <div class="ship-stat-card">
            <div class="ship-stat-icon ship-stat-icon-total">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="ship-stat-body">
                <span class="ship-stat-tag">TOTAL</span>
                <span class="ship-stat-value">{{ $totalDikirim }}</span>
                <span class="ship-stat-label">Nota Dikirim</span>
            </div>
        </div>
        <div class="ship-stat-card">
            <div class="ship-stat-icon ship-stat-icon-active">
                <i class="fas fa-truck-fast"></i>
            </div>
            <div class="ship-stat-body">
                <span class="ship-stat-tag">AKTIF</span>
                <span class="ship-stat-value">{{ $dalamPengiriman }}</span>
                <span class="ship-stat-label">Dalam Pengiriman</span>
            </div>
        </div>
        <div class="ship-stat-card">
            <div class="ship-stat-icon ship-stat-icon-done">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="ship-stat-body">
                <span class="ship-stat-tag">SELESAI</span>
                <span class="ship-stat-value">{{ $selesaiDikirim }}</span>
                <span class="ship-stat-label">Menunggu</span>
            </div>
        </div>
    </div>

    {{-- ANTREAN TABLE --}}
    <div class="ship-content-card">
        <div class="ship-toolbar">
            <h2 class="ship-content-title">Antrean Pengiriman</h2>
            <div class="ship-toolbar-right">
                <form method="GET" action="{{ route('admin.pengiriman.index') }}" class="ship-search-form" id="ship-search-form">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <div class="ship-search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari pesanan..." class="ship-search-input" id="ship-search-input">
                    </div>
                </form>
                <button class="ship-filter-btn" id="ship-filter-toggle" type="button">
                    <i class="fas fa-sliders"></i> Filter
                </button>
            </div>
        </div>

        {{-- Filter dropdown --}}
        <div class="ship-filter-panel {{ request('status') || request('search') ? 'show' : '' }}" id="ship-filter-panel">
            <form method="GET" action="{{ route('admin.pengiriman.index') }}" class="ship-filter-form" id="ship-filter-form">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
                <div class="ship-filter-group">
                    <label class="ship-filter-label">Status</label>
                    <select name="status" class="ship-filter-select" onchange="document.getElementById('ship-filter-form').submit()">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu Kirim</option>
                        <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dalam Pengiriman</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                </div>
                @if(request('status') || request('search'))
                <a href="{{ route('admin.pengiriman.index') }}" class="ship-filter-reset">
                    <i class="fas fa-times"></i> Reset
                </a>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="ship-table-wrapper">
            <table class="ship-table" id="ship-table">
                <thead>
                    <tr>
                        <th>ID PESANAN</th>
                        <th>PELANGGAN</th>
                        <th>ALAMAT PENGIRIMAN</th>
                        <th>TANGGAL</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $s)
                    @php
                        $initials = collect(explode(' ', $s->user->nama_lengkap ?? 'U'))
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->take(2)->implode('');
                        $colors = ['#1a3a17','#2D5A27','#5a9e50','#f57f17','#1565c0','#6a1b9a'];
                        $avatarColor = $colors[$s->id % count($colors)];

                        $statusClass = match($s->status_transaksi) {
                            'diproses' => 'status-menunggu',
                            'dikirim'  => 'status-dikirim',
                            'selesai'  => 'status-selesai',
                            default    => 'status-menunggu',
                        };
                        $statusLabel = match($s->status_transaksi) {
                            'diproses' => 'SIAP KIRIM',
                            'dikirim'  => 'MENGANTAR / KURIR',
                            'selesai'  => 'DITERIMA',
                            default    => strtoupper($s->status_transaksi),
                        };
                    @endphp
                    <tr>
                        <td>
                            <span class="order-id">#AWB-{{ str_pad($s->id, 7, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div class="customer-cell">
                                <div class="customer-avatar" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                                <div class="customer-info">
                                    <span class="customer-name">{{ $s->user->nama_lengkap ?? 'User' }}</span>
                                    <span class="customer-phone">{{ $s->user->no_telepon ?? '-' }}</span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="address-cell">{{ Str::limit($s->alamat_pengiriman ?? 'Belum diisi', 55) }}</span>
                        </td>
                        <td>
                            <span class="date-cell">{{ \Carbon\Carbon::parse($s->tanggal_mulai)->format('d M Y') }}</span>
                            <span class="date-sub">Pukul {{ $s->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td>
                            <span class="ship-status {{ $statusClass }}">
                                <span class="status-dot"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td>
                            <button class="btn-antar-sekarang btn-detail-ship" data-id="{{ $s->id }}" type="button">
                                <i class="fas fa-truck"></i> Antar Sekarang
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6">
                            <div class="ship-empty">
                                <i class="fas fa-truck-loading"></i>
                                <p>Tidak ada pengiriman yang ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($shipments->hasPages())
        <div class="ship-pagination-wrapper">
            <span class="pagination-info">
                Menampilkan {{ $shipments->firstItem() }} dari {{ $shipments->total() }} pesanan.
            </span>
            <div class="pagination">
                @if($shipments->onFirstPage())
                    <span class="page-link disabled"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a href="{{ $shipments->previousPageUrl() }}" class="page-link"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($shipments->getUrlRange(1, $shipments->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-link {{ $page == $shipments->currentPage() ? 'active' : '' }}">{{ $page }}</a>
                @endforeach

                @if($shipments->hasMorePages())
                    <a href="{{ $shipments->nextPageUrl() }}" class="page-link"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="page-link disabled"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
        @else
        <div class="ship-pagination-wrapper">
            <span class="pagination-info">
                Menampilkan {{ $shipments->count() }} dari {{ $shipments->total() }} pesanan.
            </span>
            <span class="pagination-info">Berikutnya →</span>
        </div>
        @endif
    </div>

    {{-- BOTTOM BANNER: Pantauan Rute --}}
    <div class="ship-bottom-grid">
        <div class="ship-banner-card">
            <div class="ship-banner-bg"></div>
            <div class="ship-banner-content">
                <h3 class="ship-banner-title">Pantauan Rute Real-time</h3>
                <p class="ship-banner-desc">Lihat lokasi armada dan optimalisasi rute pengiriman langsung dari pusat kontrol Wildwood.</p>
                <button class="ship-banner-btn" type="button">
                    <i class="fas fa-map-location-dot"></i> Buka Peta Pengiriman
                </button>
            </div>
        </div>
        <div class="ship-help-card">
            <h4 class="ship-help-title">Butuh Bantuan Rute?</h4>
            <p class="ship-help-desc">Gunakan algoritma Wildwood AI untuk membagi paket ke armada yang tersedia paling efisien.</p>
            <button class="ship-help-btn" type="button">
                Gunakan Wildwood AI
            </button>
        </div>
    </div>
</div>

{{-- ============================== --}}
{{-- MODAL: UPDATE PENGIRIMAN       --}}
{{-- ============================== --}}
<div class="modal-overlay" id="modal-shipping">
    <div class="modal-box modal-box-ship">
        {{-- Modal Header --}}
        <div class="modal-ship-header">
            <div class="modal-ship-header-bg"></div>
            <div class="modal-ship-header-content">
                <span class="modal-ship-label">Admin: Update Pengiriman (GKDL)</span>
                <h3 class="modal-ship-order-id" id="modal-ship-order-id">Order ID: GK-2024-0000</h3>
            </div>
        </div>

        {{-- Modal Body --}}
        <div class="modal-ship-body" id="modal-ship-body">
            <div class="modal-loading">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Memuat detail pengiriman...</p>
            </div>
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
        setTimeout(() => { m.style.display = 'none'; }, 300);
    }

    // Close modal on overlay click
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) closeModal(this.id);
        });
    });

    // ── Filter Toggle ──
    const filterToggle = document.getElementById('ship-filter-toggle');
    const filterPanel = document.getElementById('ship-filter-panel');
    if (filterToggle && filterPanel) {
        filterToggle.addEventListener('click', () => {
            filterPanel.classList.toggle('show');
        });
    }

    // ── Search on Enter ──
    const searchInput = document.getElementById('ship-search-input');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.getElementById('ship-search-form').submit();
            }
        });
    }

    // ── Open Detail Modal ──
    document.querySelectorAll('.btn-detail-ship').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const body = document.getElementById('modal-ship-body');

            body.innerHTML = '<div class="modal-loading"><i class="fas fa-spinner fa-spin"></i><p>Memuat detail pengiriman...</p></div>';
            document.getElementById('modal-ship-order-id').textContent = 'Order ID: GK-2024-' + String(id).padStart(4, '0');

            openModal('modal-shipping');

            fetch(`{{ url('admin/pengiriman') }}/${id}`)
                .then(r => r.json())
                .then(data => {
                    const isMenunggu = data.status_transaksi === 'diproses';
                    const isDikirim = data.status_transaksi === 'dikirim';
                    const isSelesai = data.status_transaksi === 'selesai';

                    let timelineSteps = [
                        { label: 'Pesanan Dibuat', date: data.created_at, done: true },
                        { label: 'Sedang Disiapkan', date: isMenunggu ? 'Menunggu...' : data.created_at, done: !isMenunggu },
                        { label: 'Sedang Diantar Kurir', date: isDikirim ? 'Sedang dalam perjalanan' : (isSelesai ? 'Selesai' : 'Menunggu proses sebelumnya'), done: isDikirim || isSelesai },
                        { label: 'Barang Diterima Pelanggan', date: isSelesai ? 'Diterima' : 'Menunggu konfirmasi', done: isSelesai },
                    ];

                    let timelineHtml = timelineSteps.map((s, i) => `
                        <div class="timeline-step ${s.done ? 'done' : ''}">
                            <div class="timeline-dot"><i class="fas ${s.done ? 'fa-check' : 'fa-circle'}"></i></div>
                            ${i < timelineSteps.length - 1 ? '<div class="timeline-line"></div>' : ''}
                            <div class="timeline-info">
                                <span class="timeline-label">${s.label}</span>
                                <span class="timeline-date">${s.date}</span>
                            </div>
                        </div>
                    `).join('');

                    let itemsHtml = data.items.map(item => `
                        <div class="modal-ship-item">
                            <div class="modal-ship-item-img">
                                ${item.gambar
                                    ? `<img src="/storage/${item.gambar}" alt="${item.nama}">`
                                    : `<i class="fas fa-campground"></i>`
                                }
                            </div>
                            <div class="modal-ship-item-info">
                                <span class="modal-ship-item-name">${item.nama}</span>
                                <span class="modal-ship-item-spec">${item.spek || '-'}</span>
                            </div>
                            <span class="modal-ship-item-qty">${item.jumlah} Unit</span>
                        </div>
                    `).join('');

                    let actionHtml = '';
                    if (!isSelesai) {
                        actionHtml = `
                            <div class="modal-ship-action-card">
                                <h4 class="modal-ship-action-title">Update Status Pengiriman</h4>
                                ${isMenunggu ? `
                                <form action="{{ url('admin/pengiriman') }}/${data.id}/status" method="POST" class="modal-ship-action-form">
                                    @csrf
                                    <input type="hidden" name="action" value="siapkan">
                                    <button type="submit" class="ship-action-btn ship-action-siapkan">
                                        <i class="fas fa-box"></i> Siapkan Pesanan
                                    </button>
                                </form>
                                <form action="{{ url('admin/pengiriman') }}/${data.id}/status" method="POST" class="modal-ship-action-form">
                                    @csrf
                                    <input type="hidden" name="action" value="kirim">
                                    <button type="submit" class="ship-action-btn ship-action-kirim">
                                        <i class="fas fa-truck"></i> Antar Pesanan
                                    </button>
                                </form>
                                ` : ''}
                                ${isDikirim ? `
                                <form action="{{ url('admin/pengiriman') }}/${data.id}/status" method="POST" enctype="multipart/form-data" class="modal-ship-action-form modal-ship-confirm-form">
                                    @csrf
                                    <input type="hidden" name="action" value="selesai">
                                    <div class="ship-form-group">
                                        <label class="ship-form-label">NAMA PENERIMA</label>
                                        <input type="text" name="nama_penerima" class="ship-form-input" placeholder="Masukkan nama penerima..." required>
                                    </div>
                                    <div class="ship-form-group">
                                        <label class="ship-form-label">UPLOAD BUKTI FOTO PENGIRIMAN (WAJIB)</label>
                                        <label class="ship-upload-area" id="upload-area-${data.id}">
                                            <input type="file" name="bukti_pengiriman" accept="image/*" class="ship-upload-input" onchange="previewUpload(this, 'upload-area-${data.id}')">
                                            <div class="ship-upload-placeholder">
                                                <i class="fas fa-camera"></i>
                                                <span>Klik untuk ambil foto atau upload</span>
                                                <small>Format: JPG, PNG (Maks. 5MB)</small>
                                            </div>
                                        </label>
                                    </div>
                                    <button type="submit" class="ship-action-btn ship-action-selesai">
                                        <i class="fas fa-check-circle"></i> Konfirmasi Selesai
                                    </button>
                                    <p class="ship-action-note">Ini konfirmasi bahwa pesanan sudah diterima dengan baik oleh pelanggan dan telah di konfirmasi oleh kurir.</p>
                                </form>
                                ` : ''}
                            </div>
                        `;
                    }

                    body.innerHTML = `
                        <div class="modal-ship-grid">
                            <div class="modal-ship-left">
                                <div class="modal-ship-section">
                                    <h4 class="modal-ship-section-title">
                                        <i class="fas fa-user"></i> Informasi Pelanggan
                                    </h4>
                                    <div class="modal-ship-info-grid">
                                        <div class="modal-ship-info-item">
                                            <span class="modal-ship-info-label">NAMA PELANGGAN</span>
                                            <span class="modal-ship-info-value">${data.user_nama}</span>
                                        </div>
                                        <div class="modal-ship-info-item">
                                            <span class="modal-ship-info-label">NOMOR TELEPON</span>
                                            <span class="modal-ship-info-value">${data.user_telepon}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-ship-section">
                                    <h4 class="modal-ship-section-title">
                                        <i class="fas fa-map-marker-alt"></i> Alamat Pengiriman
                                    </h4>
                                    <p class="modal-ship-address">${data.alamat_pengiriman}</p>
                                    <a href="https://maps.google.com/?q=${encodeURIComponent(data.alamat_pengiriman)}" target="_blank" class="modal-ship-maps-link">
                                        <i class="fas fa-map"></i> Periksa kami menggunakan alamat gambang ulten
                                    </a>
                                </div>
                                <div class="modal-ship-section">
                                    <h4 class="modal-ship-section-title">
                                        <i class="fas fa-boxes-stacked"></i> Daftar Barang Sewa
                                    </h4>
                                    <div class="modal-ship-items">
                                        ${itemsHtml || '<p class="text-muted">Tidak ada barang.</p>'}
                                    </div>
                                </div>
                            </div>
                            <div class="modal-ship-right">
                                <div class="modal-ship-timeline-card">
                                    <h4 class="modal-ship-section-title">STATUS PENGIRIMAN</h4>
                                    <div class="modal-ship-timeline">
                                        ${timelineHtml}
                                    </div>
                                </div>
                                ${actionHtml}
                            </div>
                        </div>
                    `;
                })
                .catch(err => {
                    body.innerHTML = '<div class="modal-loading"><i class="fas fa-exclamation-triangle"></i><p>Gagal memuat data.</p></div>';
                });
        });
    });

    // ── Table row animations ──
    document.querySelectorAll('.ship-table tbody tr').forEach((row, i) => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(12px)';
        setTimeout(() => {
            row.style.transition = 'all 0.4s cubic-bezier(0.16,1,0.3,1)';
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';
        }, 60 + i * 50);
    });

    // Stat card entrance animations
    document.querySelectorAll('.ship-stat-card').forEach((c, i) => {
        c.style.opacity = '0';
        c.style.transform = 'translateY(16px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1';
            c.style.transform = 'translateY(0)';
        }, 80 + i * 100);
    });

    // Alert auto-dismiss
    const al = document.getElementById('admin-alert');
    if (al) {
        setTimeout(() => {
            al.style.opacity = '0';
            al.style.transform = 'translateY(-10px)';
            setTimeout(() => al.remove(), 300);
        }, 4000);
    }
});

// ── Upload preview ──
function previewUpload(input, areaId) {
    const area = document.getElementById(areaId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            area.innerHTML = `
                <img src="${e.target.result}" class="ship-upload-preview">
                <input type="file" name="bukti_pengiriman" accept="image/*" class="ship-upload-input" onchange="previewUpload(this, '${areaId}')">
            `;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
