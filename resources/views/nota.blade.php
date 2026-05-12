<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Digital - #GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
            color: #1a1a1a;
            padding: 20px;
        }

        .nota-container {
            max-width: 700px;
            margin: 0 auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }

        /* Header */
        .nota-header {
            background: linear-gradient(135deg, #2d5a27 0%, #1a3a16 100%);
            color: #fff;
            padding: 32px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .nota-brand h1 {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .nota-brand p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }

        .nota-ref {
            text-align: right;
        }

        .nota-ref .ref-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.5);
        }

        .nota-ref .ref-number {
            font-size: 1.3rem;
            font-weight: 700;
            margin-top: 4px;
            display: block;
        }

        .nota-ref .ref-date {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
            margin-top: 4px;
        }

        /* Body */
        .nota-body {
            padding: 32px;
        }

        /* Info Row */
        .nota-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 28px;
            padding-bottom: 24px;
            border-bottom: 1px solid #eee;
        }

        .nota-info-box h4 {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 6px;
        }

        .nota-info-box p {
            font-size: 0.9rem;
            color: #333;
            line-height: 1.5;
        }

        /* Items Table */
        .nota-section-title {
            font-size: 0.85rem;
            font-weight: 600;
            color: #2d5a27;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 12px;
        }

        .nota-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        .nota-table thead th {
            background: #f8f9fa;
            padding: 10px 14px;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #666;
            text-align: left;
            border-bottom: 2px solid #eee;
        }

        .nota-table thead th:last-child {
            text-align: right;
        }

        .nota-table tbody td {
            padding: 12px 14px;
            font-size: 0.9rem;
            border-bottom: 1px solid #f0f0f0;
        }

        .nota-table tbody td:last-child {
            text-align: right;
            font-weight: 600;
        }

        .nota-table tbody td .item-detail {
            font-size: 0.8rem;
            color: #999;
        }

        /* Totals */
        .nota-totals {
            border-top: 2px solid #eee;
            padding-top: 16px;
            margin-bottom: 28px;
        }

        .nota-total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 0.9rem;
            color: #555;
        }

        .nota-total-row.total-final {
            border-top: 2px solid #2d5a27;
            margin-top: 8px;
            padding-top: 14px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #2d5a27;
        }

        .nota-total-row.denda {
            color: #e74c3c;
        }

        /* Status Badge */
        .nota-status {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-lunas {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .status-belum {
            background: #fff3e0;
            color: #e65100;
        }

        /* Footer */
        .nota-footer {
            background: #f8f9fa;
            padding: 24px 32px;
            text-align: center;
            border-top: 1px solid #eee;
        }

        .nota-footer p {
            font-size: 0.8rem;
            color: #999;
            line-height: 1.6;
        }

        .nota-footer strong {
            color: #666;
        }

        /* Print button */
        .print-bar {
            max-width: 700px;
            margin: 0 auto 16px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn-print {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: #2d5a27;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-print:hover {
            background: #1a3a16;
        }

        .btn-back-nota {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            background: #fff;
            color: #555;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            text-decoration: none;
            transition: background 0.3s;
        }

        .btn-back-nota:hover {
            background: #f5f5f5;
        }

        /* Print styles */
        @media print {
            body {
                background: #fff;
                padding: 0;
            }

            .nota-container {
                box-shadow: none;
                border-radius: 0;
            }

            .print-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

@php
    $durasi = $transaction->tanggal_mulai->diffInDays($transaction->tanggal_selesai);
    $subtotal = 0;
    foreach($transaction->details as $detail) {
        $subtotal += $detail->product->harga_sewa * $detail->jumlah * $durasi;
    }
    $biayaAdmin = 2500;
    $isLunas = in_array($transaction->status_transaksi, ['diproses', 'dikirim', 'selesai']);

    $statusLabels = [
        'menunggu'       => 'Menunggu Pembayaran',
        'menunggu_admin' => 'Menunggu Verifikasi',
        'diproses'       => 'Diproses',
        'dikirim'        => 'Dikirim',
        'selesai'        => 'Selesai',
        'dibatalkan'     => 'Dibatalkan',
    ];
@endphp

<!-- Print Actions -->
<div class="print-bar">
    <a href="{{ route('pesanan.detail', $transaction->id) }}" class="btn-back-nota">← Kembali</a>
    <button onclick="window.print()" class="btn-print">🖨️ Cetak / Simpan PDF</button>
</div>

<div class="nota-container">
    <!-- Header -->
    <div class="nota-header">
        <div class="nota-brand">
            <h1>🏕️ Gardakala Outdoor</h1>
            <p>Rental Peralatan Outdoor Terpercaya</p>
        </div>
        <div class="nota-ref">
            <span class="ref-label">Nomor Referensi</span>
            <span class="ref-number">#GK-{{ str_pad($transaction->id, 4, '0', STR_PAD_LEFT) }}</span>
            <span class="ref-date">{{ $transaction->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    <!-- Body -->
    <div class="nota-body">
        <!-- Info Grid -->
        <div class="nota-info-grid">
            <div class="nota-info-box">
                <h4>Pelanggan</h4>
                <p>{{ $transaction->user->name ?? 'Pelanggan' }}</p>
                <p>{{ $transaction->user->email ?? '-' }}</p>
            </div>
            <div class="nota-info-box">
                <h4>Masa Sewa</h4>
                <p>{{ $transaction->tanggal_mulai->format('d M Y') }} — {{ $transaction->tanggal_selesai->format('d M Y') }}</p>
                <p>{{ $durasi }} hari</p>
            </div>
            <div class="nota-info-box">
                <h4>Metode Pengambilan</h4>
                <p>{{ $transaction->metode_pengambilan === 'pickup' ? 'Ambil di Toko' : 'Diantar ke Alamat' }}</p>
                @if($transaction->metode_pengambilan === 'deliver' && $transaction->alamat_pengiriman)
                    <p style="font-size: 0.8rem; color: #999;">{{ $transaction->alamat_pengiriman }}</p>
                @endif
            </div>
            <div class="nota-info-box">
                <h4>Status Pembayaran</h4>
                <span class="nota-status {{ $isLunas ? 'status-lunas' : 'status-belum' }}">
                    {{ $isLunas ? '✓ Lunas' : '⏳ ' . ($statusLabels[$transaction->status_transaksi] ?? 'Pending') }}
                </span>
            </div>
        </div>

        <!-- Items Table -->
        <h3 class="nota-section-title">Rincian Sewa</h3>
        <table class="nota-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Durasi</th>
                    <th>Harga/Hari</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transaction->details as $detail)
                @php
                    $itemTotal = $detail->product->harga_sewa * $detail->jumlah * $durasi;
                @endphp
                <tr>
                    <td>
                        {{ $detail->product->nama_produk ?? 'Produk' }}
                        <div class="item-detail">{{ $detail->product->category->nama_kategori ?? '' }}</div>
                    </td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $durasi }} hari</td>
                    <td>Rp {{ number_format($detail->product->harga_sewa, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($itemTotal, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="nota-totals">
            <div class="nota-total-row">
                <span>Subtotal Sewa</span>
                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
            </div>
            <div class="nota-total-row">
                <span>Biaya Admin</span>
                <span>Rp {{ number_format($biayaAdmin, 0, ',', '.') }}</span>
            </div>
            @if($transaction->denda > 0)
            <div class="nota-total-row denda">
                <span>Denda Keterlambatan</span>
                <span>Rp {{ number_format($transaction->denda, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="nota-total-row total-final">
                <span>Total Pembayaran</span>
                <span>Rp {{ number_format($transaction->total_biaya + $transaction->denda, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($transaction->payment)
        <h3 class="nota-section-title">Info Pembayaran</h3>
        <div class="nota-info-grid" style="border-bottom: none; margin-bottom: 0; padding-bottom: 0;">
            <div class="nota-info-box">
                <h4>Metode</h4>
                <p>{{ str_replace('_', ' ', ucfirst($transaction->payment->metode_pembayaran)) }}</p>
            </div>
            <div class="nota-info-box">
                <h4>Status</h4>
                <p style="text-transform: capitalize;">{{ str_replace('_', ' ', $transaction->payment->status_pembayaran) }}</p>
            </div>
        </div>
        @endif
    </div>

    <!-- Footer -->
    <div class="nota-footer">
        <p>
            <strong>Gardakala Outdoor</strong> — Rental Peralatan Outdoor Terpercaya<br>
            Nota ini dicetak secara digital pada {{ now()->format('d M Y, H:i') }} WIB<br>
            Terima kasih telah mempercayakan petualangan Anda bersama kami! 🏔️
        </p>
    </div>
</div>

</body>
</html>
