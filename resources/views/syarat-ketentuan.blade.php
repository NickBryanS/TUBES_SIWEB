@extends('layouts.app')

@section('title', 'Syarat & Ketentuan - Gardakala Outdoor')
@section('description', 'Syarat dan ketentuan penyewaan alat camping di Gardakala Outdoor.')

@section('styles')
<style>
    .tc-container {
        max-width: 800px;
        margin: 60px auto 100px;
        padding: 0 20px;
        font-family: 'Inter', sans-serif;
    }
    .tc-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .tc-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #1c2b1a;
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }
    .tc-subtitle {
        font-size: 1rem;
        color: #6b7280;
    }
    .tc-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 40px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
    .tc-section {
        margin-bottom: 30px;
    }
    .tc-section:last-child {
        margin-bottom: 0;
    }
    .tc-section-title {
        font-family: 'Poppins', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        color: #1c2b1a;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tc-section-title i {
        color: #2D5A27;
        font-size: 1.1rem;
    }
    .tc-text {
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.7;
    }
    .tc-list {
        margin-top: 8px;
        padding-left: 20px;
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.7;
    }
    .tc-list li {
        margin-bottom: 6px;
    }
</style>
@endsection

@section('content')
<div class="tc-container">
    <div class="tc-header">
        <h1 class="tc-title">Syarat & Ketentuan</h1>
        <p class="tc-subtitle">Harap baca syarat dan ketentuan di bawah ini dengan seksama sebelum melakukan penyewaan.</p>
    </div>

    <div class="tc-card">
        <div class="tc-section">
            <h2 class="tc-section-title"><i class="fas fa-user-check"></i> 1. Persyaratan Penyewa</h2>
            <p class="tc-text">Penyewa wajib mendaftarkan akun secara valid di website Gardakala Outdoor menggunakan alamat email aktif. Penyewa wajib mengunggah foto kartu identitas diri yang sah dan masih berlaku (KTP/SIM/Kartu Pelajar) saat proses checkout pemesanan.</p>
        </div>

        <div class="tc-section">
            <h2 class="tc-section-title"><i class="fas fa-calendar-alt"></i> 2. Periode & Perpanjangan Sewa</h2>
            <p class="tc-text">Waktu sewa dihitung berdasarkan hari (24 jam) terhitung sejak tanggal mulai sewa yang disetujui. Apabila ingin memperpanjang durasi sewa, pengajuan harus dilakukan maksimal 24 jam sebelum tanggal berakhir sewa melalui website dan menunggu persetujuan admin.</p>
        </div>

        <div class="tc-section">
            <h2 class="fas-warning tc-section-title"><i class="fas fa-exclamation-triangle"></i> 3. Keterlambatan & Denda</h2>
            <p class="tc-text">Keterlambatan pengembalian barang tanpa konfirmasi persetujuan perpanjangan sewa akan dikenakan denda keterlambatan:</p>
            <ul class="tc-list">
                <li>Denda sebesar 50% dari harga sewa harian per barang untuk setiap hari keterlambatan.</li>
                <li>Denda kerusakan barang dihitung berdasarkan tingkat kerusakan fisik barang yang dinilai oleh tim admin toko saat proses pengembalian.</li>
                <li>Kehilangan barang sewaan mewajibkan penyewa mengganti rugi senilai harga beli baru barang tersebut.</li>
            </ul>
        </div>

        <div class="tc-section">
            <h2 class="tc-section-title"><i class="fas fa-shipping-fast"></i> 4. Metode Pengambilan & Pengantaran</h2>
            <p class="tc-text">Barang dapat diambil langsung di toko fisik kami secara gratis atau menggunakan jasa pengiriman kurir kami (Deliver) dengan tarif ongkos kirim flat per kilometer. Untuk metode pengantaran (Deliver), serah terima barang hanya sah jika dilakukan dengan penerima yang tertera pada invoice.</p>
        </div>

        <div class="tc-section">
            <h2 class="tc-section-title"><i class="fas fa-shield-alt"></i> 5. Tanggung Jawab Barang</h2>
            <p class="tc-text">Penyewa bertanggung jawab penuh atas kebersihan, keutuhan, dan keselamatan seluruh barang yang disewa selama masa sewa berlangsung. Barang harus dikembalikan dalam kondisi kering dan bebas dari tanah/lumpur tebal.</p>
        </div>
    </div>
</div>
@endsection
