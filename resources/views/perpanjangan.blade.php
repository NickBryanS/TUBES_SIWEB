@extends('layouts.app')

@section('title', 'Perpanjangan Sewa - Gardakala Outdoor')
@section('nav-katalog', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}">
<style>
    .perpanjangan-page {
        min-height: 100vh;
        padding: 120px 20px 60px;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
    }

    .perpanjangan-container {
        max-width: 700px;
        margin: 0 auto;
    }

    .perpanjangan-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 32px;
        backdrop-filter: blur(10px);
    }

    .perpanjangan-card h1 {
        font-size: 1.6rem;
        color: #fff;
        margin-bottom: 8px;
    }

    .perpanjangan-card .subtitle {
        color: rgba(255, 255, 255, 0.5);
        margin-bottom: 24px;
        font-size: 0.9rem;
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 12px 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
    }

    .info-row span:last-child {
        color: #fff;
        font-weight: 600;
    }

    .form-group {
        margin-top: 24px;
    }

    .form-group label {
        display: block;
        color: rgba(255, 255, 255, 0.7);
        margin-bottom: 8px;
        font-size: 0.9rem;
    }

    .form-group input {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
        outline: none;
        transition: border-color 0.3s;
    }

    .form-group input:focus {
        border-color: #e8a838;
    }

    .form-group .hint {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.8rem;
        margin-top: 6px;
    }

    .estimasi-biaya {
        margin-top: 20px;
        padding: 16px;
        background: rgba(232, 168, 56, 0.1);
        border: 1px solid rgba(232, 168, 56, 0.3);
        border-radius: 10px;
        color: #e8a838;
        font-size: 0.95rem;
        display: none;
    }

    .estimasi-biaya strong {
        display: block;
        font-size: 1.1rem;
        margin-top: 4px;
    }

    .btn-submit-perpanjangan {
        width: 100%;
        margin-top: 24px;
        padding: 14px;
        background: linear-gradient(135deg, #e8a838, #d4943a);
        border: none;
        border-radius: 10px;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .btn-submit-perpanjangan:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(232, 168, 56, 0.3);
    }

    .btn-back {
        display: inline-block;
        margin-top: 16px;
        color: rgba(255, 255, 255, 0.5);
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
    }

    .btn-back:hover {
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="perpanjangan-page">
    <div class="perpanjangan-container">
        <div class="perpanjangan-card">
            <h1><i class="fas fa-calendar-plus"></i> Perpanjangan Sewa</h1>
            <p class="subtitle">Ajukan perpanjangan durasi sewa untuk pesanan Anda.</p>

            {{-- Info Pesanan --}}
            <div class="info-row">
                <span>No. Pesanan</span>
                <span>#GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal Mulai</span>
                <span>{{ $transaction->tanggal_mulai->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span>Tanggal Selesai (Saat Ini)</span>
                <span>{{ $transaction->tanggal_selesai->format('d M Y') }}</span>
            </div>
            <div class="info-row">
                <span>Total Biaya Saat Ini</span>
                <span>Rp {{ number_format($transaction->total_biaya, 0, ',', '.') }}</span>
            </div>

            {{-- Daftar Item --}}
            <div style="margin-top: 20px;">
                @foreach($transaction->details as $detail)
                <div class="info-row">
                    <span>{{ $detail->product->nama_produk }} (x{{ $detail->jumlah }})</span>
                    <span>Rp {{ number_format($detail->product->harga_sewa, 0, ',', '.') }}/hari</span>
                </div>
                @endforeach
            </div>

            {{-- Form --}}
            <form action="{{ route('perpanjangan.store', $transaction->id) }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="perpanjangan_hari">Jumlah Hari Perpanjangan</label>
                    <input type="number" name="perpanjangan_hari" id="perpanjangan_hari"
                           min="1" max="30" value="{{ old('perpanjangan_hari', 1) }}"
                           required>
                    <p class="hint">Minimal 1 hari, maksimal 30 hari.</p>

                    @error('perpanjangan_hari')
                        <p style="color: #ff6b6b; font-size: 0.85rem; margin-top: 4px;">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Estimasi Biaya Tambahan (dihitung via JS) --}}
                <div class="estimasi-biaya" id="estimasi-biaya">
                    Estimasi biaya tambahan:
                    <strong id="estimasi-total">Rp 0</strong>
                </div>

                <button type="submit" class="btn-submit-perpanjangan">
                    <i class="fas fa-paper-plane"></i> Ajukan Perpanjangan
                </button>
            </form>

            <a href="{{ route('pesanan.detail', $transaction->id) }}" class="btn-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Detail Pesanan
            </a>
        </div>
    </div>
</div>

<script>
    // Hitung estimasi biaya tambahan secara real-time
    const inputHari = document.getElementById('perpanjangan_hari');
    const estimasiDiv = document.getElementById('estimasi-biaya');
    const estimasiTotal = document.getElementById('estimasi-total');

    // Total harga sewa harian semua item
    const totalHarian = {{ $transaction->details->sum(function($d) { return $d->product->harga_sewa * $d->jumlah; }) }};

    inputHari.addEventListener('input', function() {
        const hari = parseInt(this.value) || 0;
        if (hari > 0) {
            const total = totalHarian * hari;
            estimasiTotal.textContent = 'Rp ' + total.toLocaleString('id-ID');
            estimasiDiv.style.display = 'block';
        } else {
            estimasiDiv.style.display = 'none';
        }
    });

    // Trigger on load
    inputHari.dispatchEvent(new Event('input'));
</script>
@endsection
