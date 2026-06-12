@extends('layouts.app')

@section('title', 'Perpanjangan Sewa - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/perpanjangan.css') }}">
@endsection

@php
    $totalHarianRaw = $transaction->details->sum(function($d) { return $d->product->harga_sewa * $d->jumlah; });
    $currentDays = $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai);
@endphp

@section('content')
<div class="perpanjangan-page">
    <!-- BREADCRUMB -->
    <div class="perpanjangan-breadcrumb">
        <a href="/riwayat">Pesanan Saya</a>
        <i class="fas fa-chevron-right"></i>
        <a href="{{ route('pesanan.detail', $transaction->id) }}">Detail Pesanan</a>
        <i class="fas fa-chevron-right"></i>
        <span>Perpanjangan</span>
    </div>

    <!-- HEADER -->
    <div class="perpanjangan-header">
        <h1>Perpanjangan Sewa</h1>
        <p>Ajukan perpanjangan durasi sewa untuk pesanan #GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>

    {{-- ALERT MESSAGES --}}
    @if(session('success'))
        <div style="background: rgba(46,204,113,0.1); border: 1px solid rgba(46,204,113,0.25); color: #27ae60; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.88rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background: rgba(231,76,60,0.1); border: 1px solid rgba(231,76,60,0.25); color: #e74c3c; padding: 14px 20px; border-radius: 12px; margin-bottom: 20px; font-size: 0.88rem; display: flex; align-items: center; gap: 10px;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <!-- MAIN GRID -->
    <div class="perpanjangan-grid">
        <!-- LEFT COLUMN -->
        <div class="perpanjangan-left">
            <!-- Info Pesanan Saat Ini -->
            <div class="perpanjangan-section">
                <h3><i class="fas fa-info-circle"></i> Informasi Pesanan Saat Ini</h3>
                <div class="perpanjangan-info-line">
                    <span>No. Pesanan</span>
                    <span>#GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="perpanjangan-info-line">
                    <span>Tanggal Mulai</span>
                    <span>{{ $transaction->tanggal_mulai->format('d M Y') }}</span>
                </div>
                <div class="perpanjangan-info-line">
                    <span>Tanggal Selesai (Saat Ini)</span>
                    <span>{{ $transaction->tanggal_selesai->format('d M Y') }}</span>
                </div>
                <div class="perpanjangan-info-line">
                    <span>Durasi Sewa</span>
                    <span>{{ $currentDays }} hari</span>
                </div>
                <div class="perpanjangan-info-line total">
                    <span>Total Biaya Saat Ini</span>
                    <span>Rp {{ number_format($transaction->total_biaya, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Alat yang Disewa -->
            <div class="perpanjangan-section">
                <h3><i class="fas fa-box"></i> Alat yang Disewa</h3>
                @foreach($transaction->details as $detail)
                <div class="perpanjangan-item">
                    <div class="perpanjangan-item-img">
                        <img src="{{ asset($detail->product->url_gambar ?? 'images/placeholder.png') }}" alt="{{ $detail->product->nama_produk }}">
                    </div>
                    <div class="perpanjangan-item-info">
                        <h4>{{ $detail->product->nama_produk }}</h4>
                        <p>{{ $detail->jumlah }} unit &middot; Rp {{ number_format($detail->product->harga_sewa, 0, ',', '.') }}/hari</p>
                    </div>
                    <span class="perpanjangan-item-price">
                        Rp {{ number_format($detail->product->harga_sewa * $detail->jumlah, 0, ',', '.') }}/hari
                    </span>
                </div>
                @endforeach
            </div>

            <!-- Form Perpanjangan -->
            <div class="perpanjangan-section">
                <h3><i class="fas fa-calendar-plus"></i> Ajukan Perpanjangan</h3>

                <form action="{{ route('perpanjangan.store', $transaction->id) }}" method="POST" id="form-perpanjangan">
                    @csrf

                    <!-- Quick Day Selector -->
                    <div class="perpanjangan-form-group">
                        <label class="perpanjangan-form-label">Pilih Cepat</label>
                        <div class="day-selector">
                            <button type="button" class="day-chip" data-days="1">1 Hari</button>
                            <button type="button" class="day-chip" data-days="3">3 Hari</button>
                            <button type="button" class="day-chip" data-days="5">5 Hari</button>
                            <button type="button" class="day-chip" data-days="7">7 Hari</button>
                            <button type="button" class="day-chip" data-days="14">14 Hari</button>
                            <button type="button" class="day-chip" data-days="30">30 Hari</button>
                        </div>
                    </div>

                    <!-- Manual Input -->
                    <div class="perpanjangan-form-group">
                        <label class="perpanjangan-form-label" for="perpanjangan_hari">Jumlah Hari Perpanjangan</label>
                        <div class="perpanjangan-input-wrapper">
                            <input type="number"
                                   class="perpanjangan-input"
                                   name="perpanjangan_hari"
                                   id="perpanjangan_hari"
                                   min="1"
                                   max="30"
                                   value="{{ old('perpanjangan_hari', 1) }}"
                                   placeholder="Masukkan jumlah hari"
                                   required>
                        </div>
                        <p class="perpanjangan-hint">
                            <i class="fas fa-info-circle"></i> Minimal 1 hari, maksimal 30 hari.
                        </p>

                        @error('perpanjangan_hari')
                            <p class="perpanjangan-error">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Tanggal Baru Preview -->
                    <div class="perpanjangan-date-preview" id="date-preview">
                        <i class="fas fa-calendar-check"></i>
                        <div class="date-preview-content">
                            <div class="date-preview-label">Tanggal Selesai Baru</div>
                            <div class="date-preview-value" id="new-end-date">-</div>
                        </div>
                    </div>

                    <!-- Estimasi Biaya -->
                    <div class="perpanjangan-estimasi" id="estimasi-biaya">
                        <div class="estimasi-label">Estimasi Biaya Tambahan</div>
                        <div class="estimasi-value" id="estimasi-total">Rp 0</div>
                        <div class="estimasi-breakdown" id="estimasi-breakdown"></div>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="btn-perpanjangan-submit" id="btn-submit">
                        <i class="fas fa-paper-plane"></i> Ajukan Perpanjangan
                    </button>
                </form>

                <a href="{{ route('pesanan.detail', $transaction->id) }}" class="perpanjangan-back-link">
                    <i class="fas fa-arrow-left"></i> Kembali ke Detail Pesanan
                </a>
            </div>
        </div>

        <!-- RIGHT COLUMN - SUMMARY -->
        <div class="perpanjangan-right">
            <div class="perpanjangan-summary-card">
                <h3><i class="fas fa-receipt"></i> Ringkasan Perpanjangan</h3>

                <div class="perpanjangan-info-line">
                    <span>Harga sewa/hari</span>
                    <span>Rp {{ number_format($totalHarianRaw, 0, ',', '.') }}</span>
                </div>
                <div class="perpanjangan-info-line" id="summary-days-line">
                    <span>Tambahan hari</span>
                    <span id="summary-days">1 hari</span>
                </div>

                <hr class="summary-divider">

                <div class="perpanjangan-info-line total">
                    <span>Biaya Tambahan</span>
                    <span id="summary-total">Rp {{ number_format($totalHarianRaw, 0, ',', '.') }}</span>
                </div>

                <div class="perpanjangan-alert info">
                    <i class="fas fa-info-circle"></i>
                    <span>Perpanjangan akan menunggu persetujuan admin sebelum diproses. Pembayaran tambahan dilakukan setelah disetujui.</span>
                </div>

                <div class="perpanjangan-secure-badge">
                    <i class="fas fa-shield-alt"></i> TRANSAKSI AMAN & TERPERCAYA
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Elements
    const inputHari = document.getElementById('perpanjangan_hari');
    const estimasiDiv = document.getElementById('estimasi-biaya');
    const estimasiTotal = document.getElementById('estimasi-total');
    const estimasiBreakdown = document.getElementById('estimasi-breakdown');
    const datePreview = document.getElementById('date-preview');
    const newEndDate = document.getElementById('new-end-date');
    const summaryDays = document.getElementById('summary-days');
    const summaryTotal = document.getElementById('summary-total');
    const dayChips = document.querySelectorAll('.day-chip');

    // Data
    const totalHarian = {{ $totalHarianRaw }};
    const currentEndDate = new Date('{{ $transaction->tanggal_selesai->format("Y-m-d") }}');

    // Formatter
    function formatRupiah(num) {
        return 'Rp ' + num.toLocaleString('id-ID');
    }

    function formatDate(date) {
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        return date.getDate() + ' ' + months[date.getMonth()] + ' ' + date.getFullYear();
    }

    // Day chip click
    dayChips.forEach(chip => {
        chip.addEventListener('click', function() {
            const days = parseInt(this.dataset.days);
            inputHari.value = days;
            dayChips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            updateEstimation();
        });
    });

    // Input change
    inputHari.addEventListener('input', function() {
        const val = parseInt(this.value) || 0;
        dayChips.forEach(c => {
            c.classList.toggle('active', parseInt(c.dataset.days) === val);
        });
        updateEstimation();
    });

    function updateEstimation() {
        const hari = parseInt(inputHari.value) || 0;

        if (hari > 0) {
            const total = totalHarian * hari;

            // Estimasi box
            estimasiTotal.textContent = formatRupiah(total);
            estimasiBreakdown.textContent = formatRupiah(totalHarian) + '/hari × ' + hari + ' hari';
            estimasiDiv.style.display = 'block';

            // Date preview
            const newDate = new Date(currentEndDate);
            newDate.setDate(newDate.getDate() + hari);
            newEndDate.textContent = formatDate(newDate);
            datePreview.style.display = 'flex';

            // Summary sidebar
            summaryDays.textContent = hari + ' hari';
            summaryTotal.textContent = formatRupiah(total);
        } else {
            estimasiDiv.style.display = 'none';
            datePreview.style.display = 'none';
            summaryDays.textContent = '0 hari';
            summaryTotal.textContent = formatRupiah(0);
        }
    }

    // Trigger on load
    updateEstimation();

    // Set initial active chip
    const initialVal = parseInt(inputHari.value) || 0;
    dayChips.forEach(c => {
        if (parseInt(c.dataset.days) === initialVal) c.classList.add('active');
    });
</script>
@endsection
