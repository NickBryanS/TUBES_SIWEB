@extends('layouts.app')

@section('title', 'Manajemen Alamat - Gardakala Outdoor')
@section('description', 'Kelola daftar alamat pengiriman Anda di Gardakala Outdoor.')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/user-alamat.css') }}">
@endsection

@section('content')
<div class="alamat-page">
    <div class="alamat-container">
        {{-- BREADCRUMB --}}
        <div class="alamat-breadcrumb">
            <a href="/dashboard"><i class="fas fa-home"></i> Dashboard</a>
            <i class="fas fa-chevron-right"></i>
            <a href="{{ route('user.profil') }}">Profil</a>
            <i class="fas fa-chevron-right"></i>
            <span>Manajemen Alamat</span>
        </div>

        {{-- PAGE HEADER --}}
        <div class="alamat-header">
            <div>
                <h1>Manajemen Alamat</h1>
                <p>Kelola alamat pengiriman untuk pemesanan Anda</p>
            </div>
            <button class="btn-add-alamat" id="btn-add-alamat" onclick="openModal()">
                <i class="fas fa-plus"></i> Tambah Alamat
            </button>
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

        {{-- DAFTAR ALAMAT --}}
        @if($addresses->count() > 0)
            <div class="alamat-grid">
                @foreach($addresses as $address)
                <div class="alamat-card {{ $address->is_utama ? 'alamat-utama' : '' }}" id="alamat-{{ $address->id }}">
                    <div class="alamat-card-header">
                        <div class="alamat-label-wrap">
                            <span class="alamat-label">
                                <i class="fas {{ $address->label === 'Rumah' ? 'fa-home' : ($address->label === 'Kantor' ? 'fa-building' : 'fa-map-marker-alt') }}"></i>
                                {{ $address->label }}
                            </span>
                            @if($address->is_utama)
                                <span class="badge-utama"><i class="fas fa-star"></i> Utama</span>
                            @endif
                        </div>
                        <div class="alamat-card-actions">
                            @if(!$address->is_utama)
                                <form method="POST" action="{{ route('user.alamat.set-utama', $address) }}" style="display:inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn-action btn-set-utama" title="Jadikan Utama">
                                        <i class="fas fa-star"></i>
                                    </button>
                                </form>
                            @endif
                            <button type="button" class="btn-action btn-edit" title="Edit" onclick="openEditModal({{ json_encode($address) }})">
                                <i class="fas fa-pen"></i>
                            </button>
                            <form method="POST" action="{{ route('user.alamat.destroy', $address) }}" style="display:inline" onsubmit="return confirm('Yakin ingin menghapus alamat ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action btn-delete" title="Hapus">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    <div class="alamat-card-body">
                        <p class="alamat-nama"><strong>{{ $address->nama_penerima }}</strong></p>
                        <p class="alamat-telepon"><i class="fas fa-phone"></i> {{ $address->nomor_telepon }}</p>
                        <p class="alamat-detail">{{ $address->alamat_lengkap }}</p>
                        <p class="alamat-wilayah">{{ $address->kota }}, {{ $address->provinsi }} {{ $address->kode_pos }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="alamat-empty">
                <div class="empty-icon">
                    <i class="fas fa-map-marked-alt"></i>
                </div>
                <h3>Belum Ada Alamat</h3>
                <p>Tambahkan alamat pengiriman pertama Anda untuk mempermudah proses pemesanan.</p>
                <button class="btn-add-alamat" onclick="openModal()">
                    <i class="fas fa-plus"></i> Tambah Alamat Pertama
                </button>
            </div>
        @endif
    </div>
</div>

{{-- MODAL TAMBAH/EDIT ALAMAT --}}
<div class="modal-overlay" id="modal-overlay" onclick="closeModal(event)">
    <div class="modal-content" id="modal-content">
        <div class="modal-header">
            <h2 id="modal-title"><i class="fas fa-map-marker-alt"></i> Tambah Alamat Baru</h2>
            <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
        </div>
        <form method="POST" action="{{ route('user.alamat.store') }}" id="alamat-form">
            @csrf
            <div id="form-method-field"></div>
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="label">Label Alamat <span class="required">*</span></label>
                        <div class="label-options">
                            <label class="label-option">
                                <input type="radio" name="label" value="Rumah" checked>
                                <span><i class="fas fa-home"></i> Rumah</span>
                            </label>
                            <label class="label-option">
                                <input type="radio" name="label" value="Kantor">
                                <span><i class="fas fa-building"></i> Kantor</span>
                            </label>
                            <label class="label-option">
                                <input type="radio" name="label" value="Lainnya">
                                <span><i class="fas fa-map-pin"></i> Lainnya</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-row form-row-2">
                    <div class="form-group">
                        <label for="nama_penerima">Nama Penerima <span class="required">*</span></label>
                        <input type="text" id="nama_penerima" name="nama_penerima" placeholder="Nama lengkap penerima" required>
                    </div>
                    <div class="form-group">
                        <label for="nomor_telepon">Nomor Telepon <span class="required">*</span></label>
                        <input type="text" id="nomor_telepon_alamat" name="nomor_telepon" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="alamat_lengkap">Alamat Lengkap <span class="required">*</span></label>
                    <textarea id="alamat_lengkap" name="alamat_lengkap" rows="3" placeholder="Jl. Nama Jalan No. XX, RT/RW, Kelurahan, Kecamatan" required></textarea>
                </div>
                <div class="form-row form-row-3">
                    <div class="form-group">
                        <label for="kota">Kota <span class="required">*</span></label>
                        <input type="text" id="kota" name="kota" placeholder="Nama kota" required>
                    </div>
                    <div class="form-group">
                        <label for="provinsi">Provinsi <span class="required">*</span></label>
                        <input type="text" id="provinsi" name="provinsi" placeholder="Nama provinsi" required>
                    </div>
                    <div class="form-group">
                        <label for="kode_pos">Kode Pos <span class="required">*</span></label>
                        <input type="text" id="kode_pos" name="kode_pos" placeholder="XXXXX" required maxlength="10">
                    </div>
                </div>
                <div class="form-group checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_utama" value="1" id="is_utama">
                        <span class="checkmark"></span>
                        <span>Jadikan sebagai alamat utama</span>
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeModal()">Batal</button>
                <button type="submit" class="btn-submit" id="btn-submit-alamat">
                    <i class="fas fa-save"></i> Simpan Alamat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal() {
        document.getElementById('modal-overlay').classList.add('active');
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-map-marker-alt"></i> Tambah Alamat Baru';
        document.getElementById('alamat-form').action = "{{ route('user.alamat.store') }}";
        document.getElementById('form-method-field').innerHTML = '';
        document.getElementById('btn-submit-alamat').innerHTML = '<i class="fas fa-save"></i> Simpan Alamat';
        // Reset form
        document.getElementById('alamat-form').reset();
        document.body.style.overflow = 'hidden';
    }

    function openEditModal(address) {
        document.getElementById('modal-overlay').classList.add('active');
        document.getElementById('modal-title').innerHTML = '<i class="fas fa-pen"></i> Edit Alamat';
        document.getElementById('alamat-form').action = '/user/alamat/' + address.id;
        document.getElementById('form-method-field').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('btn-submit-alamat').innerHTML = '<i class="fas fa-save"></i> Perbarui Alamat';

        // Fill form
        document.getElementById('nama_penerima').value = address.nama_penerima;
        document.getElementById('nomor_telepon_alamat').value = address.nomor_telepon;
        document.getElementById('alamat_lengkap').value = address.alamat_lengkap;
        document.getElementById('kota').value = address.kota;
        document.getElementById('provinsi').value = address.provinsi;
        document.getElementById('kode_pos').value = address.kode_pos;
        document.getElementById('is_utama').checked = address.is_utama;

        // Set label radio
        const radios = document.querySelectorAll('input[name="label"]');
        radios.forEach(r => {
            r.checked = r.value === address.label;
        });

        document.body.style.overflow = 'hidden';
    }

    function closeModal(event) {
        if (event && event.target !== event.currentTarget) return;
        document.getElementById('modal-overlay').classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close on Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeModal();
    });

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
