@extends('superadmin.layouts.superadmin')

@section('title', 'Pengaturan & Tim - Garkadala Admin')
@section('sidebar-pengaturan', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-pengaturan.css') }}">
@endsection

@section('content')
<div class="sa-pengaturan">
    {{-- HEADER --}}
    <div class="sa-set-header">
        <div>
            <span class="sa-set-label">Pengaturan Sistem</span>
            <h1 class="sa-set-title">Pengaturan Toko</h1>
            <p class="sa-set-subtitle">Konfigurasi operasional dan kontrol admin ke jaringan Anda.</p>
        </div>
    </div>

    {{-- SECTION: Informasi Toko & Kebijakan --}}
    <div class="sa-set-grid-2">
        <div class="sa-card sa-set-section">
            <h3 class="sa-set-section-title"><i class="fas fa-store"></i> Informasi Toko Umum</h3>
            <form method="POST" action="{{ route('superadmin.pengaturan.update') }}">
                @csrf
                <div class="sa-form-grid">
                    <div class="sa-form-group full">
                        <label class="sa-form-label">NAMA TOKO</label>
                        <input type="text" name="nama_toko" class="sa-form-input"
                               value="{{ $settings['nama_toko'] ?? 'Gardakala Outdoor' }}" required>
                    </div>
                    <div class="sa-form-group">
                        <label class="sa-form-label">SINGKATAN</label>
                        <input type="text" name="singkatan_toko" class="sa-form-input"
                               value="{{ $settings['singkatan_toko'] ?? 'GKDL' }}">
                    </div>
                    <div class="sa-form-group">
                        <label class="sa-form-label">TELEPON</label>
                        <input type="text" name="telepon_toko" class="sa-form-input"
                               value="{{ $settings['telepon_toko'] ?? '' }}">
                    </div>
                    <div class="sa-form-group full">
                        <label class="sa-form-label">EMAIL OPERASIONAL</label>
                        <input type="email" name="email_toko" class="sa-form-input"
                               value="{{ $settings['email_toko'] ?? '' }}">
                    </div>
                    <div class="sa-form-group full">
                        <label class="sa-form-label">ALAMAT LENGKAP</label>
                        <textarea name="alamat_toko" class="sa-form-textarea" rows="2">{{ $settings['alamat_toko'] ?? '' }}</textarea>
                    </div>
                </div>
                <div style="margin-top:16px;text-align:right;">
                    <button type="submit" class="sa-btn-primary">
                        <i class="fas fa-save"></i> Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        <div class="sa-card sa-set-section">
            <h3 class="sa-set-section-title"><i class="fas fa-scroll"></i> Kebijakan Sewa</h3>
            <div class="sa-form-grid">
                <div class="sa-form-group">
                    <label class="sa-form-label">DURASI SEWA MINIMUM (HARI)</label>
                    <div class="sa-counter">
                        <button class="sa-counter-btn" type="button">-</button>
                        <span class="sa-counter-val">{{ $settings['min_sewa_hari'] ?? 1 }}</span>
                        <button class="sa-counter-btn" type="button">+</button>
                    </div>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">MAX. DP (%)</label>
                    <div class="sa-counter">
                        <button class="sa-counter-btn" type="button">-</button>
                        <span class="sa-counter-val">{{ $settings['max_dp_persen'] ?? 50 }}</span>
                        <button class="sa-counter-btn" type="button">+</button>
                    </div>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">DENDA KETERLAMBATAN PER HARI</label>
                    <input type="text" class="sa-form-input"
                           value="Rp {{ number_format($settings['denda_per_hari'] ?? 50000, 0, ',', '.') }}" readonly>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">SYARAT &amp; KETENTUAN</label>
                    <div class="sa-terms-list">
                        <p>1. Hanya untuk mahasiswa/WNI dengan ID yang sudah terverifikasi.</p>
                        <p>2. Jika produk rusak/hilang menjadi tanggung jawab penyewa.</p>
                        <p>3. Pembatalan &gt; 24 jam sebelum disewakan: potongan 50%.</p>
                        <p>4. Pengembalian barang harus dalam kondisi original.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: Pengaturan Pembayaran (DB-driven) --}}
    <div class="sa-card sa-set-section">
        <div class="sa-set-section-header">
            <h3 class="sa-set-section-title"><i class="fas fa-credit-card"></i> Pengaturan Metode Pembayaran</h3>
            <button class="sa-btn-primary sa-btn-sm" type="button" onclick="document.getElementById('modalTambahRekening').classList.add('show')">
                <i class="fas fa-plus"></i> Tambah Rekening
            </button>
        </div>

        <div class="sa-pay-cards">
            @forelse($paymentSettings as $ps)
            <div class="sa-bank-card-wrapper {{ !$ps->is_active ? 'inactive' : '' }}">
                <div class="sa-bank-card">
                    <div class="sa-bank-card-top">
                        <div class="sa-bank-chip"><i class="fas fa-sim-card"></i></div>
                        @if(!$ps->is_active)
                        <span class="sa-bank-inactive-badge">NONAKTIF</span>
                        @endif
                    </div>
                    <div class="sa-bank-label">BANK {{ $ps->nama_bank }}</div>
                    <div class="sa-bank-number">{{ $ps->nomor_rekening }}</div>
                    <div class="sa-bank-name">{{ $ps->atas_nama }}</div>
                    <div class="sa-bank-status">
                        <i class="fas {{ $ps->is_active ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                        {{ $ps->is_active ? 'Rekening Aktif' : 'Nonaktif' }}
                    </div>
                </div>
                <div class="sa-bank-actions">
                    <button class="sa-btn-icon sa-btn-edit" title="Edit" onclick='openEditRekening(@json($ps))'>
                        <i class="fas fa-pen"></i>
                    </button>
                    <form action="{{ route('superadmin.payment.toggle', $ps->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <button class="sa-btn-icon {{ $ps->is_active ? 'sa-btn-warn' : 'sa-btn-success' }}" title="{{ $ps->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                            <i class="fas {{ $ps->is_active ? 'fa-toggle-off' : 'fa-toggle-on' }}"></i>
                        </button>
                    </form>
                    <form action="{{ route('superadmin.payment.destroy', $ps->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus rekening {{ $ps->nama_bank }} - {{ $ps->nomor_rekening }}?')">
                        @csrf
                        @method('DELETE')
                        <button class="sa-btn-icon sa-btn-danger" title="Hapus">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @empty
            <div class="sa-empty-payment">
                <i class="fas fa-credit-card"></i>
                <p>Belum ada rekening terdaftar.</p>
                <button class="sa-btn-primary sa-btn-sm" onclick="document.getElementById('modalTambahRekening').classList.add('show')">
                    <i class="fas fa-plus"></i> Tambah Rekening Pertama
                </button>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- MODAL: TAMBAH REKENING --}}
<div class="sa-modal-overlay" id="modalTambahRekening">
    <div class="sa-modal">
        <div class="sa-modal-header">
            <h2><i class="fas fa-credit-card"></i> Tambah Rekening Bank</h2>
            <button class="sa-modal-close" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" action="{{ route('superadmin.payment.store') }}">
            @csrf
            <div class="sa-modal-body">
                <div class="sa-form-group">
                    <label class="sa-form-label">NAMA BANK</label>
                    <input type="text" name="nama_bank" class="sa-form-input" required placeholder="e.g. BCA, BNI, Mandiri">
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">NOMOR REKENING</label>
                    <input type="text" name="nomor_rekening" class="sa-form-input" required placeholder="1234567890">
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">ATAS NAMA</label>
                    <input type="text" name="atas_nama" class="sa-form-input" required placeholder="Nama pemilik rekening">
                </div>
            </div>
            <div class="sa-modal-footer">
                <button type="button" class="sa-btn-secondary" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">Batal</button>
                <button type="submit" class="sa-btn-primary"><i class="fas fa-save"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL: EDIT REKENING --}}
<div class="sa-modal-overlay" id="modalEditRekening">
    <div class="sa-modal">
        <div class="sa-modal-header">
            <h2><i class="fas fa-pen-to-square"></i> Edit Rekening Bank</h2>
            <button class="sa-modal-close" onclick="this.closest('.sa-modal-overlay').classList.remove('show')">&times;</button>
        </div>
        <form method="POST" id="formEditRekening">
            @csrf
            @method('PUT')
            <div class="sa-modal-body">
                <div class="sa-form-group">
                    <label class="sa-form-label">NAMA BANK</label>
                    <input type="text" name="nama_bank" id="editBank" class="sa-form-input" required>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">NOMOR REKENING</label>
                    <input type="text" name="nomor_rekening" id="editRekening" class="sa-form-input" required>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">ATAS NAMA</label>
                    <input type="text" name="atas_nama" id="editAtasNama" class="sa-form-input" required>
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
function openEditRekening(ps) {
    document.getElementById('formEditRekening').action = '/superadmin/pengaturan/payment/' + ps.id;
    document.getElementById('editBank').value = ps.nama_bank;
    document.getElementById('editRekening').value = ps.nomor_rekening;
    document.getElementById('editAtasNama').value = ps.atas_nama;
    document.getElementById('modalEditRekening').classList.add('show');
}

document.addEventListener('DOMContentLoaded', function() {
    // Counter buttons
    document.querySelectorAll('.sa-counter').forEach(counter => {
        const val = counter.querySelector('.sa-counter-val');
        counter.querySelectorAll('.sa-counter-btn').forEach((btn, i) => {
            btn.addEventListener('click', () => {
                let v = parseInt(val.textContent) || 0;
                val.textContent = i === 0 ? Math.max(1, v - 1) : v + 1;
            });
        });
    });

    // Close modal on overlay click
    document.querySelectorAll('.sa-modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) this.classList.remove('show');
        });
    });

    // Entrance animations
    document.querySelectorAll('.sa-set-section, .sa-card').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(12px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1'; c.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });

    const al = document.getElementById('admin-alert');
    if (al) { setTimeout(() => { al.style.opacity = '0'; setTimeout(() => al.remove(), 300); }, 4000); }
});
</script>
@endsection
