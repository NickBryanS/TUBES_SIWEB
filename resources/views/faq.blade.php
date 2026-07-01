@extends('layouts.app')

@section('title', 'Pertanyaan Umum (FAQ) - Gardakala Outdoor')
@section('description', 'Pertanyaan umum seputar penyewaan alat camping di Gardakala Outdoor.')

@section('styles')
<style>
    .faq-container {
        max-width: 800px;
        margin: 60px auto 100px;
        padding: 0 20px;
    }
    .faq-header {
        text-align: center;
        margin-bottom: 50px;
    }
    .faq-title {
        font-family: 'Poppins', sans-serif;
        font-size: 2.5rem;
        font-weight: 700;
        color: #1c2b1a;
        margin-bottom: 12px;
        letter-spacing: -0.02em;
    }
    .faq-subtitle {
        font-size: 1rem;
        color: #6b7280;
    }
    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    .faq-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.02);
    }
    .faq-item:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #2D5A27;
    }
    .faq-question {
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
        color: #1c2b1a;
        font-size: 1.05rem;
        user-select: none;
    }
    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, padding 0.3s ease;
        color: #4b5563;
        font-size: 0.95rem;
        line-height: 1.6;
        background: #fafbf9;
        border-top: 0px solid #e5e7eb;
        padding: 0 24px;
    }
    .faq-item.active .faq-answer {
        max-height: 300px;
        padding: 20px 24px;
        border-top-width: 1px;
    }
    .faq-icon {
        font-size: 0.85rem;
        color: #6b7280;
        transition: transform 0.3s ease;
    }
    .faq-item.active .faq-icon {
        transform: rotate(180s);
        color: #2D5A27;
    }
</style>
@endsection

@section('content')
<div class="faq-container">
    <div class="faq-header">
        <h1 class="faq-title">Pertanyaan Umum (FAQ)</h1>
        <p class="faq-subtitle">Punya pertanyaan seputar Gardakala Outdoor? Cari jawabannya di bawah ini.</p>
    </div>

    <div class="faq-list">
        <div class="faq-item">
            <div class="faq-question">
                <span>Bagaimana cara menyewa peralatan outdoor di Gardakala?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Anda hanya perlu mendaftar akun di website kami, pilih barang yang ingin disewa di halaman Katalog, masukkan ke keranjang belanja, tentukan tanggal sewa, pilih metode pengambilan/pengiriman, lalu lakukan pembayaran via Transfer Bank, QRIS, atau Bayar di Toko.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Jaminan apa saja yang diperlukan untuk penyewaan?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Kami mewajibkan pelanggan untuk mengunggah foto kartu identitas (KTP/SIM/Kartu Pelajar) asli saat proses checkout. Fisik kartu identitas asli tersebut wajib diserahkan atau ditunjukkan saat pengambilan barang sebagai jaminan.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Bagaimana kebijakan denda jika terlambat mengembalikan barang?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Keterlambatan pengembalian akan dikenakan denda otomatis sebesar 50% dari tarif sewa harian per item per hari keterlambatan. Denda ini dihitung secara transparan di dalam sistem dan harus dilunasi saat pengembalian barang.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Apakah bisa memperpanjang durasi sewa di tengah jalan?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Bisa. Anda dapat mengajukan perpanjangan durasi sewa secara langsung melalui halaman Detail Pesanan Anda sebelum tanggal sewa berakhir. Pengajuan perpanjangan memerlukan persetujuan dari Admin kami dan akan dikenakan biaya sewa tambahan normal.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Apakah Gardakala menyediakan layanan antar-jemput barang?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Ya! Kami menyediakan metode pengambilan "Deliver" yang menggunakan kurir toko kami untuk mengantarkan dan menjemput barang sewaan langsung ke alamat rumah Anda. Tarif pengiriman dihitung otomatis berdasarkan jarak tempuh.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question">
                <span>Bagaimana jika barang yang saya sewa rusak atau hilang?</span>
                <i class="fas fa-chevron-down faq-icon"></i>
            </div>
            <div class="faq-answer">
                Pelanggan bertanggung jawab penuh atas kondisi barang sewaan. Jika barang rusak atau hilang, pelanggan diwajibkan membayar biaya perbaikan atau penggantian sesuai dengan kebijakan denda kerusakan yang ditetapkan oleh pihak admin.
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const icon = item.querySelector('.faq-icon');
        
        question.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            
            // Close all items
            faqItems.forEach(i => {
                i.classList.remove('active');
                i.querySelector('.faq-icon').style.transform = 'rotate(0deg)';
            });

            if (!isActive) {
                item.classList.add('active');
                icon.style.transform = 'rotate(180deg)';
            }
        });
    });
});
</script>
@endsection
