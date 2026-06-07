@extends('superadmin.layouts.superadmin')

@section('title', 'Manajemen Admin - Super Admin GKDL')
@section('sidebar-admin', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-admin.css') }}">
@endsection

@section('content')
<div class="sa-admin-page">
    {{-- HEADER --}}
    <div class="sa-page-header">
        <div>
            <span class="sa-page-label">Manajemen Staff</span>
            <h1 class="sa-page-title">Kelola Akun Admin</h1>
            <p class="sa-page-subtitle">Tambah, edit, atau hapus akun admin operasional toko.</p>
        </div>
        <div class="sa-page-actions">
            <button class="sa-btn-primary" onclick="document.getElementById('modalTambah').classList.add('show')">
                <i class="fas fa-plus"></i> Tambah Admin
            </button>
        </div>
    </div>

    {{-- SEARCH BAR --}}
    <div class="sa-search-bar">
        <form method="GET" action="{{ route('superadmin.admin.index') }}" class="sa-search-form">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Cari nama atau email admin..." value="{{ request('search') }}">
            @if(request('search'))
            <a href="{{ route('superadmin.admin.index') }}" class="sa-search-clear"><i class="fas fa-times"></i></a>
            @endif
        </form>
    </div>

    {{-- STAT CARDS --}}
    <div class="sa-admin-stats">
        <div class="sa-mini-stat">
            <div class="sa-mini-stat-icon green"><i class="fas fa-users"></i></div>
            <div>
                <span class="sa-mini-stat-value">{{ $admins->count() }}</span>
                <span class="sa-mini-stat-label">Total Admin</span>
            </div>
        </div>
        <div class="sa-mini-stat">
            <div class="sa-mini-stat-icon blue"><i class="fas fa-user-check"></i></div>
            <div>
                <span class="sa-mini-stat-value">{{ $admins->where('status_akun', 'aktif')->count() }}</span>
                <span class="sa-mini-stat-label">Aktif</span>
            </div>
        </div>
        <div class="sa-mini-stat">
            <div class="sa-mini-stat-icon red"><i class="fas fa-user-slash"></i></div>
            <div>
                <span class="sa-mini-stat-value">{{ $admins->where('status_akun', 'nonaktif')->count() }}</span>
                <span class="sa-mini-stat-label">Nonaktif</span>
            </div>
        </div>
        <div class="sa-mini-stat">
            <div class="sa-mini-stat-icon gold"><i class="fas fa-crown"></i></div>
            <div>
                <span class="sa-mini-stat-value">{{ $admins->where('peran', 'superadmin')->count() }}</span>
                <span class="sa-mini-stat-label">Super Admin</span>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="sa-card sa-admin-table-card">
        <table class="sa-admin-table">
            <thead>
                <tr>
                    <th>ADMIN</th>
                    <th>TELEPON</th>
                    <th>PERAN</th>
                    <th>STATUS</th>
                    <th>TERDAFTAR</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                @php
                    $initials = collect(explode(' ', $admin->nama_lengkap ?? 'A'))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
                    $colors = ['#1a3a17','#2D5A27','#5a9e50','#c8a951','#6a1b9a'];
                    $avatarColor = $colors[$admin->id % count($colors)];
                @endphp
                <tr class="{{ $admin->status_akun === 'nonaktif' ? 'row-disabled' : '' }}">
                    <td>
                        <div class="sa-admin-cell">
                            <div class="sa-admin-avatar" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                            <div class="sa-admin-info">
                                <span class="sa-admin-name">{{ $admin->nama_lengkap }}</span>
                                <span class="sa-admin-email">{{ $admin->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="sa-admin-phone">{{ $admin->nomor_telepon ?? '-' }}</td>
                    <td>
                        <span class="sa-role-badge {{ $admin->peran === 'superadmin' ? 'role-owner' : 'role-admin' }}">
                            {{ $admin->peran === 'superadmin' ? 'Super Admin' : 'Admin' }}
                        </span>
                    </td>
                    <td>
                        <span class="sa-status-badge {{ $admin->status_akun === 'aktif' ? 'status-aktif' : 'status-nonaktif' }}">
                            <i class="fas fa-circle"></i> {{ ucfirst($admin->status_akun) }}
                        </span>
                    </td>
                    <td class="sa-admin-date">{{ $admin->created_at->format('d M Y') }}</td>
                    <td>
                        @if($admin->peran !== 'superadmin')
                        <div class="sa-action-group">
                            <button class="sa-btn-icon sa-btn-edit" title="Edit" onclick="openEditModal({{ json_encode($admin) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form action="{{ route('superadmin.admin.toggle', $admin->id) }}" method="POST" style="display:inline;">
                                @csrf
                                <button class="sa-btn-icon {{ $admin->status_akun === 'aktif' ? 'sa-btn-warn' : 'sa-btn-success' }}" title="{{ $admin->status_akun === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="fas {{ $admin->status_akun === 'aktif' ? 'fa-ban' : 'fa-check' }}"></i>
                                </button>
                            </form>
                            <form action="{{ route('superadmin.admin.destroy', $admin->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus admin {{ $admin->nama_lengkap }}?')">
                                @csrf
                                @method('DELETE')
                                <button class="sa-btn-icon sa-btn-danger" title="Hapus">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                        @else
                        <span class="sa-text-muted">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="sa-empty-text">Belum ada data admin.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- MODAL: TAMBAH ADMIN --}}
<div class="sa-modal-overlay" id="modalTambah">
    <div class="sa-modal">
        <div class="sa-modal-header">
            <h2><i class="fas fa-user-plus"></i> Tambah Admin Baru</h2>
            <button class="sa-modal-close" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" action="{{ route('superadmin.admin.store') }}">
            @csrf
            <div class="sa-modal-body">
                <div class="sa-form-group">
                    <label class="sa-form-label">NAMA LENGKAP</label>
                    <input type="text" name="nama_lengkap" class="sa-form-input" required placeholder="Masukkan nama lengkap">
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">EMAIL</label>
                    <input type="email" name="email" class="sa-form-input" required placeholder="admin@email.com">
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">NOMOR TELEPON</label>
                    <input type="text" name="nomor_telepon" class="sa-form-input" placeholder="08xxxxxxxxxx">
                </div>
                <div class="sa-form-row">
                    <div class="sa-form-group">
                        <label class="sa-form-label">PASSWORD</label>
                        <input type="password" name="password" class="sa-form-input" required minlength="6" placeholder="Min. 6 karakter">
                    </div>
                    <div class="sa-form-group">
                        <label class="sa-form-label">KONFIRMASI PASSWORD</label>
                        <input type="password" name="password_confirmation" class="sa-form-input" required placeholder="Ulangi password">
                    </div>
                </div>
            </div>
            <div class="sa-modal-footer">
                <button type="button" class="sa-btn-secondary" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">Batal</button>
                <button type="submit" class="sa-btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDIT ADMIN --}}
<div class="sa-modal-overlay" id="modalEdit">
    <div class="sa-modal">
        <div class="sa-modal-header">
            <h2><i class="fas fa-pen-to-square"></i> Edit Data Admin</h2>
            <button class="sa-modal-close" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" id="formEdit">
            @csrf
            @method('PUT')
            <div class="sa-modal-body">
                <div class="sa-form-group">
                    <label class="sa-form-label">NAMA LENGKAP</label>
                    <input type="text" name="nama_lengkap" id="editNama" class="sa-form-input" required>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">EMAIL</label>
                    <input type="email" name="email" id="editEmail" class="sa-form-input" required>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">NOMOR TELEPON</label>
                    <input type="text" name="nomor_telepon" id="editTelp" class="sa-form-input">
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">PASSWORD BARU <small>(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="sa-form-input" minlength="6" placeholder="Kosongkan jika tetap">
                </div>
            </div>
            <div class="sa-modal-footer">
                <button type="button" class="sa-btn-secondary" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">Batal</button>
                <button type="submit" class="sa-btn-primary"><i class="fas fa-save"></i> Perbarui</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openEditModal(admin) {
    document.getElementById('formEdit').action = '/superadmin/admin/' + admin.id;
    document.getElementById('editNama').value = admin.nama_lengkap;
    document.getElementById('editEmail').value = admin.email;
    document.getElementById('editTelp').value = admin.nomor_telepon || '';
    document.getElementById('modalEdit').classList.add('show');
}

document.addEventListener('DOMContentLoaded', function() {
    // Close modal on overlay click
    document.querySelectorAll('.sa-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });

    // Entrance animations
    document.querySelectorAll('.sa-mini-stat').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(12px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1'; c.style.transform = 'translateY(0)';
        }, 80 + i * 80);
    });

    // Auto dismiss alerts
    const al = document.getElementById('admin-alert');
    if (al) { setTimeout(() => { al.style.opacity = '0'; setTimeout(() => al.remove(), 300); }, 4000); }

    @if($errors->any())
    document.getElementById('modalTambah').classList.add('show');
    @endif
});
</script>
@endsection
