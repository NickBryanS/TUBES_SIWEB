<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Penyewaan - {{ $periodeLabel }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; color: #1a1a1a; font-size: 12px; line-height: 1.6; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 3px solid #1a3a17; padding-bottom: 15px; }
        .header h1 { font-size: 20px; color: #1a3a17; margin-bottom: 4px; }
        .header p { color: #6b7280; font-size: 11px; }
        .header .logo { font-size: 24px; font-weight: 800; color: #1a3a17; letter-spacing: -0.02em; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 24px; }
        .summary-card { background: #f8faf8; border: 1px solid #e0e8e0; border-radius: 8px; padding: 12px 16px; text-align: center; width: 30%; }
        .summary-card .label { font-size: 10px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; }
        .summary-card .value { font-size: 16px; font-weight: 700; color: #1a3a17; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th { background: #1a3a17; color: #fff; padding: 10px 12px; text-align: left; font-size: 10px; text-transform: uppercase; letter-spacing: 0.05em; }
        td { padding: 9px 12px; border-bottom: 1px solid #e8e8e8; font-size: 11px; }
        tr:nth-child(even) { background: #fafaf8; }
        .status { display: inline-block; padding: 2px 8px; border-radius: 10px; font-size: 10px; font-weight: 600; }
        .status-selesai { background: #e8f5e9; color: #2D5A27; }
        .status-diproses { background: #e3f2fd; color: #1565c0; }
        .status-dikirim { background: #fff3e0; color: #e65100; }
        .status-dibatalkan { background: #fce4ec; color: #c62828; }
        .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e8e8e8; padding-top: 12px; }
        .text-right { text-align: right; }
        .font-bold { font-weight: 700; }
        .print-btn { position: fixed; top: 20px; right: 20px; background: #1a3a17; color: #fff; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer; font-size: 13px; font-weight: 600; z-index: 100; }
        .print-btn:hover { background: #2D5A27; }
        @media print { .print-btn { display: none; } }
    </style>
</head>
<body>
    <button class="print-btn" onclick="window.print()">
        <span>🖨️ Cetak / Simpan PDF</span>
    </button>

    <div class="header">
        <div class="logo">⛰ GARDAKALA OUTDOOR</div>
        <h1>Laporan Penyewaan Alat Outdoor</h1>
        <p>Periode: {{ $periodeLabel }} | Dicetak: {{ now()->format('d M Y, H:i') }} WIB</p>
    </div>

    <div class="summary">
        <div class="summary-card">
            <div class="label">Total Pendapatan</div>
            <div class="value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
        </div>
        <div class="summary-card">
            <div class="label">Jumlah Transaksi</div>
            <div class="value">{{ $transactions->count() }} Transaksi</div>
        </div>
        <div class="summary-card">
            <div class="label">Total Denda</div>
            <div class="value">Rp {{ number_format($totalDenda, 0, ',', '.') }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>ID Transaksi</th>
                <th>Tanggal</th>
                <th>Pelanggan</th>
                <th>Produk</th>
                <th>Total Biaya</th>
                <th>Denda</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($transactions as $i => $trx)
            @php
                $produkList = $trx->details->map(fn($d) => ($d->product->nama_produk ?? '-') . ' (x' . $d->jumlah . ')')->implode(', ');
                $statusClass = match($trx->status_transaksi) {
                    'selesai' => 'status-selesai',
                    'diproses' => 'status-diproses',
                    'dikirim' => 'status-dikirim',
                    'dibatalkan' => 'status-dibatalkan',
                    default => '',
                };
            @endphp
            <tr>
                <td>{{ $i + 1 }}</td>
                <td class="font-bold">WB-{{ str_pad($trx->id, 8, '0', STR_PAD_LEFT) }}</td>
                <td>{{ $trx->created_at->format('d/m/Y') }}</td>
                <td>{{ $trx->user->nama_lengkap ?? '-' }}</td>
                <td>{{ Str::limit($produkList, 40) }}</td>
                <td class="text-right">Rp {{ number_format($trx->total_biaya, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($trx->denda ?? 0, 0, ',', '.') }}</td>
                <td><span class="status {{ $statusClass }}">{{ strtoupper($trx->status_transaksi) }}</span></td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center;padding:20px;color:#9ca3af;">Tidak ada data transaksi.</td></tr>
            @endforelse
        </tbody>
        @if($transactions->count() > 0)
        <tfoot>
            <tr style="background:#f0f4f0;font-weight:700;">
                <td colspan="5" class="text-right">TOTAL</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($totalDenda, 0, ',', '.') }}</td>
                <td></td>
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
