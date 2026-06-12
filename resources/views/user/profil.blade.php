@extends('layouts.app')

@section('title', 'Profil & Pengaturan - Gardakala Outdoor')
@section('description', 'Kelola profil dan pengaturan akun Gardakala Outdoor Anda.')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-profil.css') }}">
@endsection

@section('content')
<div class="profil-page">
    <div class="profil-container">
        {{-- BREADCRUMB --}}
        <div class="profil-breadcrumb">
            <a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <span>Profil & Pengaturan</span>
        </div>

        {{-- PAGE HEADER --}}
        <div class="profil-header">
            <div>
                <h1>Profil & Pengaturan</h1>
                <p>Kelola informasi akun dan pengaturan pribadi Anda</p>
            </div>
        </div>

        {{-- ALERT MESSAGE --}}
        @if(session('success'))
            <div class="alert alert-success" id="alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" id="alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <span>{{ $error }}</span><br>
                    @endforeach
                </div>
                <button class="alert-close" onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            </div>
        @endif

        <div class="profil-grid">
            {{-- LEFT: FOTO PROFIL --}}
            <div class="profil-sidebar">
                <div class="profil-card foto-card">
                    <div class="foto-section">
                        <div class="foto-preview" id="foto-preview">
                            <img src="{{ $user->getFotoProfilUrl() }}" alt="Foto Profil" id="foto-img">
                            <div class="foto-overlay" onclick="document.getElementById('foto-input').click()">
                                <i class="fas fa-camera"></i>
                                <span>Ubah Foto</span>
                            </div>
                        </div>
                        <h3 class="foto-name">{{ $user->nama_lengkap }}</h3>
                        <p class="foto-email">{{ $user->email }}</p>
                        <div class="foto-badges">
                            @if($user->status_verifikasi)
                                <span class="badge badge-verified"><i class="fas fa-check-circle"></i> Terverifikasi</span>
                            @else
                                <span class="badge badge-unverified"><i class="fas fa-exclamation-circle"></i> Belum Verifikasi</span>
                            @endif
                            <span class="badge badge-role">
                                <i class="fas fa-user"></i> {{ ucfirst($user->peran ?? 'user') }}
                            </span>
                        </div>
                    </div>
                    <div class="foto-actions">
                        <form method="POST" action="{{ route('user.profil.update') }}" enctype="multipart/form-data" id="foto-form">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="nama_lengkap" value="{{ $user->nama_lengkap }}">
                            <input type="hidden" name="nomor_telepon" value="{{ $user->nomor_telepon }}">
                            <input type="file" id="foto-input" name="foto_profil" accept="image/*" style="display:none" onchange="previewFoto(this)">
                            <button type="button" class="btn-foto btn-upload" onclick="document.getElementById('foto-input').click()">
                                <i class="fas fa-upload"></i> Upload Foto
                            </button>
                        </form>
                        @if($user->foto_profil)
                        <form method="POST" action="{{ route('user.profil.remove-foto') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-foto btn-remove">
                                <i class="fas fa-trash-alt"></i> Hapus Foto
                            </button>
                        </form>
                        @endif
                    </div>
                    <p class="foto-hint">Format: JPG, PNG, WebP. Maks 2MB</p>
                </div>

                {{-- QUICK LINKS --}}
                <div class="profil-card quick-links-card">
                    <h3><i class="fas fa-link"></i> Menu Cepat</h3>
                    <ul class="quick-links">
                        <li><a href="{{ route('user.alamat') }}"><i class="fas fa-map-marker-alt"></i> Manajemen Alamat</a></li>
                        <li><a href="/riwayat"><i class="fas fa-history"></i> Riwayat Pesanan</a></li>
                        <li><a href="/wishlist"><i class="fas fa-heart"></i> Wishlist</a></li>
                        <li><a href="/keranjang"><i class="fas fa-shopping-cart"></i> Keranjang</a></li>
                    </ul>
                </div>
            </div>

            {{-- RIGHT: FORM EDIT --}}
            <div class="profil-main">
                {{-- INFORMASI PRIBADI --}}
                <div class="profil-card form-card">
                    <div class="card-header">
                        <h2><i class="fas fa-user-edit"></i> Informasi Pribadi</h2>
                        <p>Perbarui nama dan nomor telepon Anda</p>
                    </div>
                    <form method="POST" action="{{ route('user.profil.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-user"></i>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="email_display">Email</label>
                            <div class="input-icon input-disabled">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="email_display" value="{{ $user->email }}" disabled>
                            </div>
                            <span class="form-hint">Email tidak dapat diubah</span>
                        </div>
                        <div class="form-group">
                            <label for="nomor_telepon">Nomor Telepon</label>
                            <div class="input-icon">
                                <i class="fas fa-phone"></i>
                                <input type="text" id="nomor_telepon" name="nomor_telepon" value="{{ old('nomor_telepon', $user->nomor_telepon) }}" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary" id="btn-save-profile">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>

                {{-- UBAH PASSWORD --}}
                <div class="profil-card form-card">
                    <div class="card-header">
                        <h2><i class="fas fa-lock"></i> Ubah Password</h2>
                        <p>Pastikan akun Anda menggunakan password yang kuat</p>
                    </div>
                    <form method="POST" action="{{ route('user.profil.password') }}">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label for="current_password">Password Saat Ini <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-key"></i>
                                <input type="password" id="current_password" name="current_password" required>
                                <button type="button" class="toggle-pass" onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="password">Password Baru <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password" name="password" required minlength="6">
                                <button type="button" class="toggle-pass" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <span class="form-hint">Minimal 6 karakter</span>
                        </div>
                        <div class="form-group">
                            <label for="password_confirmation">Konfirmasi Password Baru <span class="required">*</span></label>
                            <div class="input-icon">
                                <i class="fas fa-lock"></i>
                                <input type="password" id="password_confirmation" name="password_confirmation" required>
                                <button type="button" class="toggle-pass" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary" id="btn-change-password">
                                <i class="fas fa-shield-alt"></i> Ubah Password
                            </button>
                        </div>
                    </form>
                </div>

                {{-- INFO AKUN --}}
                <div class="profil-card info-card">
                    <div class="card-header">
                        <h2><i class="fas fa-info-circle"></i> Informasi Akun</h2>
                    </div>
                    <div class="info-grid">
                        <div class="info-item">
                            <span class="info-label">Status Akun</span>
                            <span class="info-value status-{{ $user->status_akun ?? 'aktif' }}">
                                <i class="fas fa-circle"></i> {{ ucfirst($user->status_akun ?? 'Aktif') }}
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Bergabung Sejak</span>
                            <span class="info-value">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Verifikasi Identitas</span>
                            <span class="info-value">
                                @if($user->status_verifikasi)
                                    <span class="text-green"><i class="fas fa-check"></i> Sudah Diverifikasi</span>
                                @else
                                    <span class="text-amber"><i class="fas fa-clock"></i> Belum Diverifikasi</span>
                                @endif
                            </span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Login Google</span>
                            <span class="info-value">
                                @if($user->google_id)
                                    <span class="text-green"><i class="fab fa-google"></i> Terhubung</span>
                                @else
                                    <span class="text-muted"><i class="fas fa-times"></i> Tidak Terhubung</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function previewFoto(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('foto-img').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
            // Auto submit after selecting file
            setTimeout(() => {
                document.getElementById('foto-form').submit();
            }, 500);
        }
    }

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.parentElement.querySelector('.toggle-pass i');
        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }

    // Auto-hide alerts after 5s
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(a => {
            a.style.opacity = '0';
            a.style.transform = 'translateY(-10px)';
            setTimeout(() => a.remove(), 300);
        });
    }, 5000);
</script>
@endsection
