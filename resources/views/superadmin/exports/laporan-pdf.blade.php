<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan - {{ $periodeLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1a1a1a; font-size: 12px;
            line-height: 1.6; padding: 24px;
            background: #fff;
        }

        /* ── Print Button ── */
        .print-btn {
            position: fixed; top: 20px; right: 20px;
            background: #1a3a17; color: #fff; border: none;
            padding: 10px 22px; border-radius: 8px;
            cursor: pointer; font-size: 13px; font-weight: 600;
            z-index: 999; display: flex; align-items: center; gap: 8px;
            box-shadow: 0 4px 12px rgba(26,58,23,0.3);
        }
        .print-btn:hover { background: #2D5A27; }
        @media print { .print-btn { display: none !important; } }

        /* ── Header ── */
        .header {
            text-align: center; margin-bottom: 28px;
            border-bottom: 3px solid #1a3a17; padding-bottom: 18px;
        }
        .header .logo-row {
            display: flex; align-items: center; justify-content: center;
            gap: 10px; margin-bottom: 8px;
        }
        .header .logo-icon {
            width: 40px; height: 40px; border-radius: 10px;
            background: #1a3a17; color: #8bc34a;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .header .logo-text {
            font-size: 22px; font-weight: 800;
            color: #1a3a17; letter-spacing: -0.02em;
        }
        .header h1 {
            font-size: 15px; color: #374151;
            font-weight: 600; margin-bottom: 4px;
        }
        .header .meta {
            font-size: 10px; color: #9ca3af;
        }

        /* ── Summary Cards ── */
        .summary {
            display: flex; gap: 14px;
            margin-bottom: 24px;
        }
        .summary-card {
            flex: 1; background: #f8faf8;
            border: 1.5px solid #c8e6c9; border-radius: 10px;
            padding: 14px 18px;
        }
        .summary-card.card-dark {
            background: #1a3a17; border-color: #1a3a17;
        }
        .summary-card .s-label {
            font-size: 9px; font-weight: 700;
            letter-spacing: 0.07em; color: #6b7280;
            text-transform: uppercase; margin-bottom: 6px;
        }
        .summary-card.card-dark .s-label { color: rgba(255,255,255,0.55); }
        .summary-card .s-value {
            font-size: 18px; font-weight: 800;
            color: #1a3a17;
        }
        .summary-card.card-dark .s-value { color: #fff; }

        /* ── Payment Methods row ── */
        .payment-row {
            display: flex; gap: 14px; margin-bottom: 24px;
        }
        .payment-item {
            flex: 1; background: #f9fafb;
            border: 1px solid #e5e7eb; border-radius: 8px;
            padding: 10px 14px;
        }
        .payment-item .p-label { font-size: 10px; color: #6b7280; }
        .payment-item .p-count { font-size: 13px; font-weight: 700; color: #1a3a17; }

        /* ── Table ── */
        .table-title {
            font-size: 11px; font-weight: 700;
            color: #374151; letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 10px; padding-bottom: 6px;
            border-bottom: 1px solid #e5e7eb;
        }
        table { width: 100%; border-collapse: collapse; font-size: 10.5px; }
        thead tr { background: #1a3a17; }
        th {
            color: #fff; padding: 9px 10px;
            text-align: left; font-size: 9px;
            text-transform: uppercase; letter-spacing: 0.06em;
            font-weight: 700;
        }
        th.text-right { text-align: right; }
        td { padding: 8px 10px; border-bottom: 1px solid #f0f2ee; }
        tr:nth-child(even) td { background: #f9faf8; }
        tfoot td {
            background: #e8f5e9 !important;
            font-weight: 700; font-size: 11px;
            border-top: 2px solid #c8e6c9;
        }
        .text-right { text-align: right; }
        .font-bold  { font-weight: 700; }
        .trx-id { font-family: monospace; font-weight: 700; color: #1a3a17; }

        /* ── Status badges ── */
        .status {
            display: inline-block; padding: 2px 8px;
            border-radius: 10px; font-size: 9px; font-weight: 700;
        }
        .status-selesai    { background: #e8f5e9; color: #2D5A27; }
        .status-diproses   { background: #e3f2fd; color: #1565c0; }
        .status-dikirim    { background: #fff3e0; color: #e65100; }
        .status-dibatalkan { background: #fce4ec; color: #c62828; }
        .status-menunggu   { background: #fef3c7; color: #92400e; }

        /* ── Footer ── */
        .footer {
            margin-top: 28px; text-align: center;
            font-size: 9.5px; color: #9ca3af;
            border-top: 1px solid #e5e7eb; padding-top: 12px;
        }

        /* ── Watermark ── */
        .watermark {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 140px;
            color: rgba(26, 58, 23, 0.09); /* Warna hijau gelap khas Gardakala dengan opacity lebih tinggi */
            z-index: -1;
            white-space: nowrap;
            pointer-events: none;
            font-weight: 900;
            font-family: 'Inter', Arial, sans-serif;
            text-transform: uppercase;
            letter-spacing: 20px;
            -webkit-text-stroke: 2px rgba(26, 58, 23, 0.15); /* Memberikan efek garis tepi agar semakin tebal */
        }
    </style>
</head>
<body>
    <div class="watermark">GARKADALA</div>

    <button class="print-btn" onclick="window.print()">
        🖨️ Cetak / Simpan PDF
    </button>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="logo-row">
            <div class="logo-icon">⛰</div>
            <span class="logo-text">GARDAKALA OUTDOOR</span>
        </div>
        <h1>Laporan Keuangan Penyewaan Alat Outdoor</h1>
        <div class="meta">
            Periode: <strong>{{ $periodeLabel }}</strong> &nbsp;|&nbsp;
            Dicetak: {{ now()->translatedFormat('d F Y, H:i') }} WIB &nbsp;|&nbsp;
            Diekspor Oleh: <strong>{{ $exportedBy ?? 'Sistem' }}</strong>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="summary">
        <div class="summary-card card-dark">
            <div class="s-label">Total Pendapatan</div>
            <div class="s-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Jumlah Transaksi</div>
            <div class="s-value">{{ $jumlahTransaksi }}</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Item Disewa</div>
            <div class="s-value">{{ $totalItem }} Unit</div>
        </div>
        <div class="summary-card">
            <div class="s-label">Total Denda</div>
            <div class="s-value" style="color:#c62828;">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        </div>
    </div>

    {{-- ── PAYMENT METHOD SUMMARY ── --}}
    @if($metodePembayaranSummary->isNotEmpty())
    <div class="payment-row">
        @foreach($metodePembayaranSummary as $metode => $info)
        <div class="payment-item">
            <div class="p-label">{{ ucwords(str_replace('_', ' ', $metode)) }}</div>
            <div class="p-count">{{ $info['count'] }} Transaksi &nbsp;|&nbsp; Rp {{ number_format($info['total'], 0, ',', '.') }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── TRANSACTION TABLE ── --}}
    <div class="table-title">Rincian Transaksi</div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th class="text-right">Total Biaya</th>
                <th class="text-right">Denda</th>
                <th>Metode</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $trx)
            @php
                $produkList = $trx->details->map(fn($d) => ($d->product->nama_produk ?? '-') . ' (x' . $d->jumlah . ')')->implode(', ');
                $statusClass = match($trx->status_transaksi) {
                    'selesai'        => 'status-selesai',
                    'diproses'       => 'status-diproses',
                    'dikirim'        => 'status-dikirim',
                    'dibatalkan'     => 'status-dibatalkan',
                    'menunggu',
                    'menunggu_admin' => 'status-menunggu',
                    default          => '',
                };
                $statusLabel = match($trx->status_transaksi) {
                    'menunggu_admin' => 'VERIFIKASI',
                    default => strtoupper($trx->status_transaksi),
                };
            @endphp
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td class="trx-id">#GKD-{{ str_pad($trx->id, 5, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                <td>{{ $trx->user->nama_lengkap ?? '-' }}</td>
                <td>{{ Str::limit($produkList, 45) }}</td>
                <td class="text-right font-bold">Rp {{ number_format($trx->total_biaya, 0, ',', '.') }}</td>
                <td class="text-right">{{ $trx->denda > 0 ? 'Rp '.number_format($trx->denda, 0, ',', '.') : '-' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $trx->payment->metode_pembayaran ?? '-')) }}</td>
                <td><span class="status {{ $statusClass }}">{{ $statusLabel }}</span></td>
            </tr>
            @empty
            <tr>
                <td colspan="9" style="text-align:center;padding:24px;color:#9ca3af;">
                    Tidak ada data transaksi untuk periode ini.
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr>
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <p>Laporan ini dibuat secara otomatis oleh sistem Gardakala Outdoor Management.</p>
        <p>© {{ date('Y') }} Gardakala Outdoor — Semua hak dilindungi.</p>
    </div>
</body>
</html>
