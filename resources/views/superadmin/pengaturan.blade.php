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
            <h1 class="sa-set-title">Manajemen Toko & Tim</h1>
            <p class="sa-set-subtitle">Konfigurasi operasional dan kontrol admin ke jaringan Anda.</p>
        </div>
        <div class="sa-set-actions">
            <button class="sa-btn-secondary" type="button"><i class="fas fa-undo"></i> Reset</button>
            <button class="sa-btn-primary" type="button"><i class="fas fa-save"></i> Simpan Perubahan</button>
        </div>
    </div>

    {{-- SECTION: Informasi Toko & Kebijakan --}}
    <div class="sa-set-grid-2">
        <div class="sa-card sa-set-section">
            <h3 class="sa-set-section-title"><i class="fas fa-store"></i> Informasi Toko Umum</h3>
            <div class="sa-form-grid">
                <div class="sa-form-group full">
                    <label class="sa-form-label">NAMA TOKO</label>
                    <input type="text" class="sa-form-input" value="Summit Peak Rentals" readonly>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">SINGKATAN</label>
                    <input type="text" class="sa-form-input" value="SPR" readonly>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">TELEPON</label>
                    <input type="text" class="sa-form-input" value="+62 812 3456 7890" readonly>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">EMAIL OPERASIONAL</label>
                    <input type="email" class="sa-form-input" value="ops@summitpeak.id" readonly>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">ALAMAT LENGKAP</label>
                    <textarea class="sa-form-textarea" rows="2" readonly>Jl. Pinus Hijau No. 42, Komplek Rimba Raya, Kecamatan Cisarua, Kabupaten Bogor, Jawa Barat, 16750</textarea>
                </div>
            </div>
        </div>

        <div class="sa-card sa-set-section">
            <h3 class="sa-set-section-title"><i class="fas fa-scroll"></i> Kebijakan Sewa</h3>
            <div class="sa-form-grid">
                <div class="sa-form-group">
                    <label class="sa-form-label">DURASI SEWA (HARI)</label>
                    <div class="sa-counter">
                        <button class="sa-counter-btn" type="button">-</button>
                        <span class="sa-counter-val">1</span>
                        <button class="sa-counter-btn" type="button">+</button>
                    </div>
                </div>
                <div class="sa-form-group">
                    <label class="sa-form-label">MAX. DP (%)</label>
                    <div class="sa-counter">
                        <button class="sa-counter-btn" type="button">-</button>
                        <span class="sa-counter-val">50</span>
                        <button class="sa-counter-btn" type="button">+</button>
                    </div>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">DENDA KETERLAMBATAN PER HARI</label>
                    <input type="text" class="sa-form-input" value="Rp 50.000" readonly>
                </div>
                <div class="sa-form-group full">
                    <label class="sa-form-label">SYARAT & KETENTUAN</label>
                    <div class="sa-terms-list">
                        <p>1. Hanya untuk mahasiswa/WNI dengan ID yang sudah terverifikasi.</p>
                        <p>2. Jika produk rusak/hilang menjadi tanggung jawab penyewa.</p>
                        <p>3. Pembatalan > 24 jam sebelum disewakan: potongan 50%.</p>
                        <p>4. Pengembalian barang harus dalam kondisi original.</p>
                    </div>
                    <button class="sa-link-btn" type="button"><i class="fas fa-pen"></i> Edit Data Lengkap</button>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: Pembayaran --}}
    <div class="sa-card sa-set-section">
        <h3 class="sa-set-section-title"><i class="fas fa-credit-card"></i> Pengaturan Pembayaran</h3>
        <div class="sa-pay-grid">
            <div class="sa-pay-methods">
                <label class="sa-form-label">METODE TERSEDIA</label>
                <div class="sa-check-list">
                    <label class="sa-check-item"><input type="checkbox" checked disabled> <span>Transfer Bank (Manual)</span></label>
                    <label class="sa-check-item"><input type="checkbox" checked disabled> <span>Transfer Tiket</span></label>
                    <label class="sa-check-item"><input type="checkbox" disabled> <span>Virtual Account (Otomatis)</span></label>
                    <label class="sa-check-item"><input type="checkbox" disabled> <span>E-Wallet (QRIS)</span></label>
                </div>
            </div>
            <div class="sa-pay-bank-card">
                <div class="sa-bank-card">
                    <div class="sa-bank-chip"><i class="fas fa-sim-card"></i></div>
                    <div class="sa-bank-label">BANK BCA</div>
                    <div class="sa-bank-number">8 8 3 2 &nbsp; **** &nbsp; **** &nbsp; **99</div>
                    <div class="sa-bank-name">SUMMIT PEAK ADVENTURE</div>
                    <div class="sa-bank-status"><i class="fas fa-check-circle"></i> Terkoneksi Rekening</div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION: Tim --}}
    <div class="sa-card sa-set-section">
        <div class="sa-set-section-header">
            <h3 class="sa-set-section-title"><i class="fas fa-users-gear"></i> Manajemen Tim</h3>
            <button class="sa-btn-primary sa-btn-sm" type="button"><i class="fas fa-plus"></i> TAMBAH ANGGOTA TIM</button>
        </div>
        <table class="sa-team-table">
            <thead>
                <tr>
                    <th>NAMA ANGGOTA</th>
                    <th>POSISI</th>
                    <th>TERAKHIR AKTIF</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                @forelse($timAdmin as $ta)
                @php
                    $initials = collect(explode(' ', $ta->nama_lengkap ?? 'A'))->map(fn($w)=>strtoupper(substr($w,0,1)))->take(2)->implode('');
                    $colors = ['#1a3a17','#2D5A27','#5a9e50','#c8a951','#6a1b9a'];
                    $avatarColor = $colors[$ta->id % count($colors)];
                    $posisi = $ta->peran === 'superadmin' ? 'Pemilik' : 'Admin Operasional';
                    $lastActive = $ta->updated_at ? $ta->updated_at->diffForHumans() : 'Tidak diketahui';
                @endphp
                <tr>
                    <td>
                        <div class="sa-team-cell">
                            <div class="sa-team-avatar" style="background:{{ $avatarColor }};">{{ $initials }}</div>
                            <div class="sa-team-info">
                                <span class="sa-team-name">{{ $ta->nama_lengkap }}</span>
                                <span class="sa-team-email">{{ $ta->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td><span class="sa-team-role {{ $ta->peran === 'superadmin' ? 'role-owner' : 'role-admin' }}">{{ $posisi }}</span></td>
                    <td class="sa-team-active">{{ $lastActive }}</td>
                    <td>
                        @if($ta->peran !== 'superadmin')
                        <button class="sa-btn-icon" title="Edit"><i class="fas fa-pen"></i></button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="sa-empty-text">Belum ada anggota tim.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
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

    // Entrance animations
    document.querySelectorAll('.sa-set-section, .sa-card').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(12px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1'; c.style.transform = 'translateY(0)';
        }, 100 + i * 80);
    });
});
</script>
@endsection
