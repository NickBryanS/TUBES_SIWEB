@extends('admin.layouts.admin')

@section('title', 'Kategori Produk - Garkadala Admin')
@section('sidebar-kategori', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/kategori.css') }}">
@endsection

@section('content')
<div class="kategori-page">
    {{-- HEADER --}}
    <div class="kategori-header">
        <div>
            <h1 class="kategori-title">Manajemen Kategori</h1>
            <p class="kategori-subtitle">Kelola kategori produk alat outdoor Garkadala.</p>
        </div>
        <button class="btn-tambah-kategori" id="btn-tambah-kategori">
            <i class="fas fa-plus"></i> Tambah Kategori Baru
        </button>
    </div>

    {{-- STAT CARDS --}}
    <div class="kategori-stats">
        <div class="kt-stat-card kt-stat-primary">
            <div class="kt-stat-label">TOTAL KATEGORI</div>
            <div class="kt-stat-value">{{ $totalKategori }}<span class="kt-stat-unit">Kategori</span></div>
        </div>
        <div class="kt-stat-card kt-stat-success">
            <div class="kt-stat-label">KATEGORI AKTIF</div>
            <div class="kt-stat-value">{{ $kategoriAktif }}<span class="kt-stat-unit">Kategori</span></div>
        </div>
        <div class="kt-stat-card kt-stat-warning">
            <div class="kt-stat-label">KATEGORI KOSONG</div>
            <div class="kt-stat-value">{{ $kategoriKosong }}<span class="kt-stat-unit">Kategori</span></div>
        </div>
    </div>

    {{-- DAFTAR KATEGORI --}}
    <div class="kategori-content">
        <div class="kategori-toolbar">
            <h2 class="kategori-subtitle-2">Daftar Kategori</h2>
            <div class="toolbar-right">
                <form method="GET" action="{{ route('admin.kategori.index') }}" class="toolbar-search" id="search-form">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" name="search" class="search-input" placeholder="Cari kategori..." value="{{ request('search') }}">
                </form>
                <span class="toolbar-info">{{ $categories->total() }} kategori ditemukan</span>
            </div>
        </div>

        {{-- TABEL --}}
        <div class="kategori-table-wrapper">
            <table class="kategori-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Jumlah Produk</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $index => $category)
                    <tr data-id="{{ $category->id }}">
                        <td>{{ $categories->firstItem() + $index }}</td>
                        <td>
                            <span class="kategori-name">
                                <i class="fas fa-tag"></i>{{ $category->nama_kategori }}
                            </span>
                        </td>
                        <td>
                            @if($category->products_count > 0)
                                <span class="badge-count badge-count-active">
                                    <i class="fas fa-box"></i> {{ $category->products_count }} Produk
                                </span>
                            @else
                                <span class="badge-count badge-count-empty">
                                    <i class="fas fa-box-open"></i> 0 Produk
                                </span>
                            @endif
                        </td>
                        <td><span class="date-text">{{ $category->created_at->format('d M Y') }}</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn-action btn-edit" data-id="{{ $category->id }}" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-action btn-delete" data-id="{{ $category->id }}" data-name="{{ $category->nama_kategori }}" data-count="{{ $category->products_count }}" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <i class="fas fa-tags"></i>
                                <p>Belum ada kategori produk</p>
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
                Menampilkan {{ $categories->firstItem() ?? 0 }}-{{ $categories->lastItem() ?? 0 }} dari {{ $categories->total() }} kategori
            </span>
            @if($categories->hasPages())
            <nav>
                <ul class="pagination">
                    @if ($categories->onFirstPage())
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-left"></i></span></li>
                    @else
                        <li class="page-item"><a class="page-link" href="{{ $categories->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a></li>
                    @endif

                    @foreach ($categories->getUrlRange(1, $categories->lastPage()) as $page => $url)
                        @if ($page == $categories->currentPage())
                            <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach

                    @if ($categories->hasMorePages())
                        <li class="page-item"><a class="page-link" href="{{ $categories->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a></li>
                    @else
                        <li class="page-item disabled"><span class="page-link"><i class="fas fa-chevron-right"></i></span></li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- MODAL TAMBAH/EDIT KATEGORI --}}
<div class="kt-modal-overlay" id="modal-form">
    <div class="kt-modal-box">
        <div class="kt-modal-header">
            <h3 id="modal-title">Tambah Kategori Baru</h3>
            <button class="kt-modal-close" data-close="modal-form">&times;</button>
        </div>
        <form id="form-kategori">
            @csrf
            <input type="hidden" id="kategori-id" name="kategori_id">
            <input type="hidden" id="form-method" name="_method" value="POST">
            <div class="kt-modal-body">
                <div class="kt-form-group">
                    <label class="kt-form-label">Nama Kategori <span class="required">*</span></label>
                    <input type="text" class="kt-form-control" id="nama_kategori" name="nama_kategori" placeholder="Masukkan nama kategori" required>
                    <span class="kt-form-error error-nama_kategori"></span>
                </div>
            </div>
            <div class="kt-modal-footer">
                <button type="button" class="kt-btn kt-btn-secondary" data-close="modal-form">Batal</button>
                <button type="submit" class="kt-btn kt-btn-primary" id="btn-submit">
                    <i class="fas fa-save"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL KONFIRMASI HAPUS --}}
<div class="kt-modal-overlay" id="modal-delete">
    <div class="kt-modal-box" style="max-width: 420px;">
        <div class="kt-modal-header">
            <h3>Konfirmasi Hapus</h3>
            <button class="kt-modal-close" data-close="modal-delete">&times;</button>
        </div>
        <div class="delete-modal-body">
            <div class="delete-icon"><i class="fas fa-trash-alt"></i></div>
            <h4>Hapus Kategori?</h4>
            <p id="delete-category-name">Kategori ini akan dihapus permanen.</p>
            <div class="delete-warning">
                <i class="fas fa-exclamation-triangle"></i>
                Kategori yang masih memiliki produk tidak dapat dihapus.
            </div>
        </div>
        <div class="kt-modal-footer" style="justify-content: center;">
            <button type="button" class="kt-btn kt-btn-secondary" data-close="modal-delete">Batal</button>
            <button type="button" class="kt-btn kt-btn-danger" id="btn-confirm-delete">
                <i class="fas fa-trash"></i> Hapus
            </button>
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
        document.querySelectorAll('.kt-form-error').forEach(el => el.textContent = '');
    }
    function showErrors(errors) {
        for (const field in errors) {
            const el = document.querySelector(`.error-${field}`);
            if (el) el.textContent = errors[field][0];
        }
    }

    // ── Close Modal ──
    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', () => closeModal(btn.dataset.close));
    });
    document.querySelectorAll('.kt-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closeModal(overlay.id);
        });
    });

    // ── Tambah Kategori ──
    document.getElementById('btn-tambah-kategori').addEventListener('click', () => {
        document.getElementById('form-kategori').reset();
        document.getElementById('kategori-id').value = '';
        document.getElementById('form-method').value = 'POST';
        document.getElementById('modal-title').innerText = 'Tambah Kategori Baru';
        clearErrors();
        openModal('modal-form');
    });

    // ── Edit Kategori ──
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            fetch(`{{ url('admin/kategori') }}/${id}`)
                .then(r => r.json())
                .then(cat => {
                    document.getElementById('kategori-id').value = cat.id;
                    document.getElementById('nama_kategori').value = cat.nama_kategori;
                    document.getElementById('form-method').value = 'PUT';
                    document.getElementById('modal-title').innerText = 'Edit Kategori';
                    clearErrors();
                    openModal('modal-form');
                })
                .catch(() => showToast('Gagal memuat data kategori', true));
        });
    });

    // ── Hapus Kategori ──
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            deleteId = this.dataset.id;
            const name = this.dataset.name;
            document.getElementById('delete-category-name').textContent = `Kategori "${name}" akan dihapus permanen.`;
            openModal('modal-delete');
        });
    });

    document.getElementById('btn-confirm-delete').addEventListener('click', function() {
        if (!deleteId) return;
        fetch(`{{ url('admin/kategori') }}/${deleteId}`, {
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
    document.getElementById('form-kategori').addEventListener('submit', function(e) {
        e.preventDefault();
        clearErrors();

        const kategoriId = document.getElementById('kategori-id').value;
        const method = document.getElementById('form-method').value;
        const url = method === 'PUT'
            ? `{{ url('admin/kategori') }}/${kategoriId}`
            : `{{ route('admin.kategori.store') }}`;

        const body = { nama_kategori: document.getElementById('nama_kategori').value };

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

    // ── Keyboard shortcuts ──
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.kt-modal-overlay.active').forEach(m => closeModal(m.id));
        }
    });
});
</script>
@endsection
