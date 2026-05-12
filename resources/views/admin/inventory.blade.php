@extends('admin.layouts.admin')

@section('title', 'Inventaris - Garkadala Admin')
@section('sidebar-inventaris', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/inventory.css') }}">
@endsection

@section('content')
<div class="inventory-page">
    {{-- HEADER --}}
    <div class="inventory-header">
        <div>
            <h1 class="inventory-title">Manajemen Inventaris</h1>
            <p class="inventory-subtitle">Kelola ketersediaan alat pendakian dan perlengkapan outdoor Garkadala.</p>
        </div>
        <button class="btn-tambah-alat" id="btn-tambah-alat">
            <i class="fas fa-plus"></i> Tambah Alat Baru
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="inventory-stats">
        <div class="stat-card">
            <div class="stat-label">TOTAL ALAT</div>
            <div class="stat-value">{{ number_format($totalStokSum) }}<span class="stat-unit">Unit</span></div>
        </div>
        <div class="stat-card stat-card-success">
            <div class="stat-label">TERSEDIA</div>
            <div class="stat-value">{{ number_format($tersediaSum) }}<span class="stat-unit">Unit</span></div>
        </div>
        <div class="stat-card stat-card-info">
            <div class="stat-label">DISEWA</div>
            <div class="stat-value">{{ number_format($disewaSum) }}<span class="stat-unit">Unit</span></div>
        </div>
        <div class="stat-card stat-card-warning">
            <div class="stat-label">DIPERBAIKI</div>
            <div class="stat-value">{{ number_format($diperbaiki) }}<span class="stat-unit">Unit</span></div>
        </div>
    </div>

    {{-- DAFTAR INVENTARIS --}}
    <div class="inventory-content">
        <div class="inventory-toolbar">
            <h2 class="inventory-subtitle-2">Daftar Inventaris</h2>
            <div class="toolbar-actions">
                <form method="GET" action="{{ route('admin.inventory.index') }}" class="toolbar-filters" id="filter-form">
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    <div class="filter-group">
                        <select name="category" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="filter-group">
                        <select name="status" class="filter-select" onchange="document.getElementById('filter-form').submit()">
                            <option value="">Semua Status</option>
                            <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="stok_tipis" {{ request('status') == 'stok_tipis' ? 'selected' : '' }}>Stok Tipis</option>
                            <option value="habis" {{ request('status') == 'habis' ? 'selected' : '' }}>Habis</option>
                        </select>
                    </div>
                </form>
                <button class="btn-export" id="btn-export">
                    <i class="fas fa-download"></i> Ekspor
                </button>
            </div>
        </div>

        {{-- TABEL INVENTARIS --}}
        <div class="inventory-table-wrapper">
            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nama Alat</th>
                        <th>Kategori</th>
                        <th>Harga/Hari</th>
                        <th>Total</th>
                        <th>Tersedia</th>
                        <th>Disewa</th>
                        <th>Rusak</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    @php
                        $disewa_count = $product->total_stok - $product->stok_tersedia;
                        $rusak_count = 0;
                        
                        if ($product->stok_tersedia <= 0) {
                            $status = 'Habis';
                            $status_class = 'status-habis';
                        } elseif ($product->stok_tersedia <= 5) {
                            $status = 'Stok Tipis';
                            $status_class = 'status-stok-tipis';
                        } else {
                            $status = 'Tersedia';
                            $status_class = 'status-tersedia';
                        }
                    @endphp
                    <tr class="inventory-row" data-id="{{ $product->id }}">
                        <td>
                            <div class="product-img">
                                @if($product->url_gambar)
                                    <img src="{{ $product->url_gambar }}" alt="{{ $product->nama_produk }}">
                                @else
                                    <div class="product-img-placeholder">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td><span class="product-name">{{ $product->nama_produk }}</span></td>
                        <td>
                            <span class="badge badge-category">{{ $product->category->nama_kategori ?? '-' }}</span>
                        </td>
                        <td>Rp {{ number_format($product->harga_sewa, 0, ',', '.') }}</td>
                        <td>{{ $product->total_stok }}</td>
                        <td class="col-tersedia">{{ $product->stok_tersedia }}</td>
                        <td class="col-disewa">{{ $disewa_count }}</td>
                        <td class="col-rusak">{{ $rusak_count }}</td>
                        <td>
                            <span class="status-badge {{ $status_class }}">
                                <span class="status-dot"></span>
                                {{ $status }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-view" data-id="{{ $product->id }}" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn-action btn-edit" data-id="{{ $product->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" data-id="{{ $product->id }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="fas fa-inbox"></i>
                                <p>Belum ada produk dalam inventaris</p>
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
                Menampilkan {{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }} dari {{ $products->total() }} alat
            </span>
            @if($products->hasPages())
            <nav>
                <ul class="pagination">
                    @if ($products->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $products->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
                        @if ($page == $products->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($products->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $products->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- MODAL FORM TAMBAH/EDIT --}}
<div class="modal-overlay" id="modal-form">
    <div class="modal-box">
        <div class="modal-box-header">
            <h3 id="modal-title">Tambah Alat Baru</h3>
            <button class="modal-close-btn" data-close="modal-form">&times;</button>
        </div>
        <form id="form-product">
            @csrf
            <input type="hidden" id="product-id" name="product_id">
            <input type="hidden" id="form-method" name="_method" value="POST">
            <div class="modal-box-body">
                <div class="form-group">
                    <label class="form-label">Nama Alat <span class="required">*</span></label>
                    <input type="text" class="form-control" id="nama_produk" name="nama_produk" placeholder="Masukkan nama alat" required>
                    <span class="form-error error-nama_produk"></span>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori <span class="required">*</span></label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->nama_kategori }}</option>
                            @endforeach
                        </select>
                        <span class="form-error error-category_id"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Harga Sewa/Hari <span class="required">*</span></label>
                        <input type="number" class="form-control" id="harga_sewa" name="harga_sewa" min="0" placeholder="0" required>
                        <span class="form-error error-harga_sewa"></span>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Total Stok <span class="required">*</span></label>
                        <input type="number" class="form-control" id="total_stok" name="total_stok" min="1" placeholder="0" required>
                        <span class="form-error error-total_stok"></span>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Stok Tersedia</label>
                        <input type="number" class="form-control" id="stok_tersedia" name="stok_tersedia" min="0" placeholder="Sama dengan total stok">
                        <span class="form-error error-stok_tersedia"></span>
                        <span class="form-hint">Kosongkan jika sama dengan total stok</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3" placeholder="Deskripsi alat outdoor..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Spesifikasi Teknis</label>
                    <textarea class="form-control" id="spesifikasi_teknis" name="spesifikasi_teknis" rows="3" placeholder="Spesifikasi teknis alat..."></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">URL Gambar</label>
                    <input type="url" class="form-control" id="url_gambar" name="url_gambar" placeholder="https://example.com/image.jpg">
                </div>
            </div>
            <div class="modal-box-footer">
                <button type="button" class="btn btn-secondary" data-close="modal-form">Batal</button>
                <button type="submit" class="btn btn-primary" id="btn-submit">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="modal-overlay" id="modal-delete">
    <div class="modal-box" style="max-width: 420px;">
        <div class="modal-box-header">
            <h3>Konfirmasi Hapus</h3>
            <button class="modal-close-btn" data-close="modal-delete">&times;</button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-icon"><i class="fas fa-trash-alt"></i></div>
            <h4>Hapus Produk?</h4>
            <p>Tindakan ini tidak dapat dibatalkan. Produk akan dihapus permanen dari katalog.</p>
        </div>
        <div class="modal-box-footer" style="justify-content: center;">
            <button type="button" class="btn btn-secondary" data-close="modal-delete">Batal</button>
            <button type="button" class="btn btn-danger" id="btn-confirm-delete">
                <i class="fas fa-trash"></i> Hapus
            </button>
        </div>
    </div>
</div>

{{-- MODAL DETAIL PRODUK --}}
<div class="modal-overlay" id="modal-detail">
    <div class="modal-box" style="max-width: 520px;">
        <div class="modal-box-header">
            <h3>Detail Produk</h3>
            <button class="modal-close-btn" data-close="modal-detail">&times;</button>
        </div>
        <div class="modal-box-body" id="detail-content">
            <p style="text-align:center; color:#9ca3af;">Memuat...</p>
        </div>
    </div>
</div>

{{-- TOAST CONTAINER --}}
<div class="toast-container" id="toast-container"></div>

@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('input[name="_token"]').value;
    let deleteId = null;

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
    function clearErrors() {
        document.querySelectorAll('.form-error').forEach(el => el.textContent = '');
    }
    function showErrors(errors) {
        for (const field in errors) {
            const el = document.querySelector(`.error-${field}`);
            if (el) el.textContent = errors[field][0];
        }
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

    // ── Tambah Alat ──
    document.getElementById('btn-tambah-alat').addEventListener('click', () => {
        document.getElementById('form-product').reset();
        document.getElementById('product-id').value = '';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('modal-title').innerText = 'Tambah Alat Baru';
        document.getElementById('stok_tersedia').value = '';
        clearErrors();
        openModal('modal-form');
    });

    // ── Edit Alat ──
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ url('admin/inventory') }}/${id}`)
                .then(r => r.json())
                .then(p => {
                    document.getElementById('product-id').value = p.id;
                    document.getElementById('nama_produk').value = p.nama_produk;
                    document.getElementById('category_id').value = p.category_id;
                    document.getElementById('harga_sewa').value = p.harga_sewa;
                    document.getElementById('total_stok').value = p.total_stok;
                    document.getElementById('stok_tersedia').value = p.stok_tersedia;
                    document.getElementById('deskripsi').value = p.deskripsi || '';
                    document.getElementById('spesifikasi_teknis').value = p.spesifikasi_teknis || '';
                    document.getElementById('url_gambar').value = p.url_gambar || '';
                    document.getElementById('form-method').value = 'PUT';
                    document.getElementById('modal-title').innerText = 'Edit Alat';
                    clearErrors();
                    openModal('modal-form');
                })
                .catch(() => showToast('Gagal memuat data produk', true));
        });
    });

    // ── Detail Produk ──
    document.querySelectorAll('.btn-view').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            document.getElementById('detail-content').innerHTML = '<p style="text-align:center;color:#9ca3af;"><i class="fas fa-spinner fa-spin"></i> Memuat...</p>';
            openModal('modal-detail');

            fetch(`{{ url('admin/inventory') }}/${id}`)
                .then(r => r.json())
                .then(p => {
                    const disewa = p.total_stok - p.stok_tersedia;
                    let statusHtml, statusLabel;
                    if (p.stok_tersedia <= 0) {
                        statusHtml = '<span class="status-badge status-habis"><span class="status-dot"></span> Habis</span>';
                    } else if (p.stok_tersedia <= 5) {
                        statusHtml = '<span class="status-badge status-stok-tipis"><span class="status-dot"></span> Stok Tipis</span>';
                    } else {
                        statusHtml = '<span class="status-badge status-tersedia"><span class="status-dot"></span> Tersedia</span>';
                    }

                    document.getElementById('detail-content').innerHTML = `
                        ${p.url_gambar ? `<div style="text-align:center;margin-bottom:20px;"><img src="${p.url_gambar}" alt="${p.nama_produk}" style="max-height:180px;border-radius:12px;object-fit:cover;"></div>` : ''}
                        <h3 style="font-size:1.15rem;font-weight:700;margin:0 0 4px;">${p.nama_produk}</h3>
                        <div style="margin-bottom:16px;">${p.category ? `<span class="badge badge-category">${p.category.nama_kategori}</span>` : ''} ${statusHtml}</div>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:16px;">
                            <div style="background:#f9fafb;padding:12px 16px;border-radius:8px;">
                                <div style="font-size:0.7rem;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Harga Sewa/Hari</div>
                                <div style="font-size:1.1rem;font-weight:700;color:#111827;">Rp ${Number(p.harga_sewa).toLocaleString('id-ID')}</div>
                            </div>
                            <div style="background:#f9fafb;padding:12px 16px;border-radius:8px;">
                                <div style="font-size:0.7rem;color:#6b7280;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Total Stok</div>
                                <div style="font-size:1.1rem;font-weight:700;color:#111827;">${p.total_stok} Unit</div>
                            </div>
                            <div style="background:#ecfdf5;padding:12px 16px;border-radius:8px;">
                                <div style="font-size:0.7rem;color:#065f46;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Tersedia</div>
                                <div style="font-size:1.1rem;font-weight:700;color:#059669;">${p.stok_tersedia} Unit</div>
                            </div>
                            <div style="background:#eff6ff;padding:12px 16px;border-radius:8px;">
                                <div style="font-size:0.7rem;color:#1e40af;font-weight:600;text-transform:uppercase;letter-spacing:0.05em;">Disewa</div>
                                <div style="font-size:1.1rem;font-weight:700;color:#1d4ed8;">${disewa} Unit</div>
                            </div>
                        </div>
                        ${p.deskripsi ? `<div style="margin-bottom:12px;"><div style="font-size:0.78rem;font-weight:600;color:#374151;margin-bottom:4px;">Deskripsi</div><p style="font-size:0.84rem;color:#6b7280;line-height:1.6;margin:0;">${p.deskripsi}</p></div>` : ''}
                        ${p.spesifikasi_teknis ? `<div><div style="font-size:0.78rem;font-weight:600;color:#374151;margin-bottom:4px;">Spesifikasi Teknis</div><p style="font-size:0.84rem;color:#6b7280;line-height:1.6;margin:0;">${p.spesifikasi_teknis}</p></div>` : ''}
                    `;
                })
                .catch(() => {
                    document.getElementById('detail-content').innerHTML = '<p style="text-align:center;color:#ef4444;">Gagal memuat detail produk</p>';
                });
        });
    });

    // ── Hapus Alat ──
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteId = this.dataset.id;
            openModal('modal-delete');
        });
    });

    document.getElementById('btn-confirm-delete').addEventListener('click', function() {
        if (!deleteId) return;
        fetch(`{{ url('admin/inventory') }}/${deleteId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Content-Type': 'application/json', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            closeModal('modal-delete');
            if (data.success) {
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Terjadi kesalahan', true);
            }
        })
        .catch(() => { closeModal('modal-delete'); showToast('Terjadi kesalahan', true); });
    });

    // ── Submit Form ──
    document.getElementById('form-product').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const productId = document.getElementById('product-id').value;
        const method = document.getElementById('form-method').value;
        const url = method === 'PUT'
            ? `{{ url('admin/inventory') }}/${productId}`
            : `{{ route('admin.inventory.store') }}`;

        const formData = new FormData(this);
        if (!formData.get('stok_tersedia')) {
            formData.set('stok_tersedia', formData.get('total_stok'));
        }

        // Build JSON body for proper method handling
        const body = {};
        formData.forEach((v, k) => { if (k !== '_token' && k !== '_method' && k !== 'product_id') body[k] = v; });

        fetch(url, {
            method: method === 'PUT' ? 'PUT' : 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify(body)
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                closeModal('modal-form');
                showToast(data.message);
                setTimeout(() => location.reload(), 800);
            } else if (data.errors) {
                showErrors(data.errors);
            } else {
                showToast(data.message || 'Terjadi kesalahan', true);
            }
        })
        .catch(() => showToast('Terjadi kesalahan', true));
    });

    // ── Export ──
    document.getElementById('btn-export').addEventListener('click', () => {
        window.location.href = `{{ route('admin.inventory.export') }}`;
    });

    // ── Keyboard shortcuts ──
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
});
</script>
@endsection
