<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Data Pengguna - Gardakala Outdoor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 11px;
            color: #1a1a1a;
            background: #fff;
            padding: 24px;
        }

        /* ── Print Button ── */
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: #1a3a17;
            color: #fff;
            padding: 10px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 999;
            box-shadow: 0 2px 8px rgba(0,0,0,.3);
        }
        .print-bar span { font-size: 13px; font-weight: 600; }
        .btn-print {
            background: #fff;
            color: #1a3a17;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .btn-print:hover { background: #e8f5e9; }
        .btn-back {
            background: transparent;
            color: #fff;
            border: 1px solid rgba(255,255,255,.4);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            text-decoration: none;
        }
        .btn-back:hover { background: rgba(255,255,255,.1); }

        /* ── Content area pushed below fixed bar ── */
        .page-content { margin-top: 56px; }

        /* ── Header ── */
        .doc-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 3px solid #1a3a17;
            padding-bottom: 14px;
            margin-bottom: 20px;
        }
        .doc-logo {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .doc-logo-icon {
            width: 44px; height: 44px;
            background: #1a3a17;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 20px;
        }
        .doc-logo-text h1 {
            font-size: 16px;
            font-weight: 800;
            color: #1a3a17;
            letter-spacing: -.5px;
        }
        .doc-logo-text p { font-size: 10px; color: #6b7280; }
        .doc-meta { text-align: right; font-size: 10px; color: #6b7280; line-height: 1.7; }
        .doc-meta strong { color: #1a1a1a; display: block; font-size: 13px; margin-bottom: 2px; }

        /* ── Summary Cards ── */
        .summary-row {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .summary-card {
            flex: 1;
            background: #f0f7f0;
            border: 1px solid #c8e6c9;
            border-radius: 8px;
            padding: 12px 14px;
            text-align: center;
        }
        .summary-card .s-val {
            font-size: 22px;
            font-weight: 800;
            color: #1a3a17;
            display: block;
        }
        .summary-card .s-lbl {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #5a9e50;
            font-weight: 600;
        }
        .summary-card.red { background: #fef2f2; border-color: #fca5a5; }
        .summary-card.red .s-val { color: #c62828; }
        .summary-card.red .s-lbl { color: #ef4444; }
        .summary-card.blue { background: #eff6ff; border-color: #93c5fd; }
        .summary-card.blue .s-val { color: #1565c0; }
        .summary-card.blue .s-lbl { color: #3b82f6; }
        .summary-card.orange { background: #fff7ed; border-color: #fdba74; }
        .summary-card.orange .s-val { color: #c2410c; }
        .summary-card.orange .s-lbl { color: #f97316; }

        /* ── Table ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr {
            background: #1a3a17;
            color: #fff;
        }
        th {
            padding: 9px 10px;
            text-align: left;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: .06em;
            font-weight: 600;
            white-space: nowrap;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #e8e8e8;
            vertical-align: middle;
        }
        tr:nth-child(even) td { background: #fafaf8; }
        tr:hover td { background: #f0f7f0; }

        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
        }
        .badge-aktif    { background: #e8f5e9; color: #2D5A27; }
        .badge-banned   { background: #fce4ec; color: #c62828; }
        .badge-nonaktif { background: #f3f4f6; color: #6b7280; }
        .badge-verified   { background: #e3f2fd; color: #1565c0; }
        .badge-unverified { background: #fff3e0; color: #e65100; }

        /* ── Footer ── */
        .doc-footer {
            margin-top: 24px;
            padding-top: 12px;
            border-top: 1px solid #e8e8e8;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            color: #9ca3af;
        }

        /* ── Print styles ── */
        @media print {
            .print-bar { display: none !important; }
            .page-content { margin-top: 0 !important; }
            body { padding: 16px; }
            tr:hover td { background: inherit; }
            @page {
                size: A4 landscape;
                margin: 12mm 10mm;
            }
        }
    </style>
</head>
<body>

    {{-- Fixed Print Bar --}}
    <div class="print-bar">
        <a href="javascript:history.back()" class="btn-back">← Kembali</a>
        <span>📄 Data Pengguna — Gardakala Outdoor</span>
        <button class="btn-print" onclick="window.print()">
            🖨️ Cetak / Simpan PDF
        </button>
    </div>

    <div class="page-content">

        {{-- Header --}}
        <div class="doc-header">
            <div class="doc-logo">
                <div class="doc-logo-icon">⛰</div>
                <div class="doc-logo-text">
                    <h1>GARDAKALA OUTDOOR</h1>
                    <p>Sistem Manajemen Penyewaan Alat Outdoor</p>
                </div>
            </div>
            <div class="doc-meta">
                <strong>LAPORAN DATA PENGGUNA</strong>
                Dicetak: {{ now()->format('d M Y, H:i') }} WIB<br>
                Total: {{ $totalPengguna }} pengguna terdaftar
            </div>
        </div>

        {{-- Summary --}}
        <div class="summary-row">
            <div class="summary-card">
                <span class="s-val">{{ $totalPengguna }}</span>
                <span class="s-lbl">Total Pengguna</span>
            </div>
            <div class="summary-card blue">
                <span class="s-val">{{ $aktif }}</span>
                <span class="s-lbl">Akun Aktif</span>
            </div>
            <div class="summary-card orange">
                <span class="s-val">{{ $terverifikasi }}</span>
                <span class="s-lbl">Terverifikasi</span>
            </div>
            <div class="summary-card red">
                <span class="s-val">{{ $diblokir }}</span>
                <span class="s-lbl">Diblokir</span>
            </div>
        </div>

        {{-- Table --}}
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>ID Pengguna</th>
                    <th>Nama Lengkap</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Status Akun</th>
                    <th>Verifikasi</th>
                    <th>Transaksi</th>
                    <th>Tanggal Daftar</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $user)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>GRK-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $user->nama_lengkap }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->nomor_telepon ?? '-' }}</td>
                    <td>
                        @if($user->status_akun === 'aktif')
                            <span class="badge badge-aktif">Aktif</span>
                        @elseif($user->status_akun === 'banned')
                            <span class="badge badge-banned">Diblokir</span>
                        @else
                            <span class="badge badge-nonaktif">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        @if($user->status_verifikasi)
                            <span class="badge badge-verified">Terverifikasi</span>
                        @else
                            <span class="badge badge-unverified">Belum</span>
                        @endif
                    </td>
                    <td style="text-align:center; font-weight:700;">{{ $user->transactions_count }}</td>
                    <td>{{ $user->created_at->format('d/m/Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; padding:24px; color:#9ca3af;">
                        Belum ada data pengguna.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{-- Footer --}}
        <div class="doc-footer">
            <span>Gardakala Outdoor Management System &copy; {{ date('Y') }}</span>
            <span>Dokumen ini digenerate otomatis pada {{ now()->format('d M Y, H:i') }} WIB</span>
            <span>Total {{ $totalPengguna }} pengguna</span>
        </div>

    </div>

    {{-- Auto-trigger print dialog --}}
    <script>
        window.addEventListener('load', function () {
            // Beri waktu 800ms agar layout render sempurna sebelum print
            setTimeout(function () {
                window.print();
            }, 800);
        });
    </script>

</body>
</html>
