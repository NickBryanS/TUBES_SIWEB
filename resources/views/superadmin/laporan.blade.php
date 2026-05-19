@extends('superadmin.layouts.superadmin')

@section('title', 'Laporan & Analisis - Admin GKDL')
@section('sidebar-laporan', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-laporan.css') }}">
@endsection

@section('content')
<div class="sa-laporan">
    {{-- HEADER --}}
    <div class="sa-lap-header">
        <div>
            <span class="sa-lap-label">Laporan & Analisis</span>
            <h1 class="sa-lap-title">Ringkasan Performa Toko</h1>
        </div>
        <div class="sa-lap-actions">
            <a href="{{ route('superadmin.laporan.excel', ['periode' => $periode]) }}" class="sa-export-btn">
                <i class="fas fa-file-excel"></i> Ekspor Excel
            </a>
            <a href="{{ route('superadmin.laporan.pdf', ['periode' => $periode]) }}" target="_blank" class="sa-export-btn sa-export-pdf">
                <i class="fas fa-file-pdf"></i> Ekspor PDF
            </a>
        </div>
    </div>

    {{-- PERIOD TABS --}}
    <div class="sa-period-tabs">
        <a href="{{ route('superadmin.laporan', ['periode' => 'mingguan']) }}" class="sa-period-tab {{ $periode === 'mingguan' ? 'active' : '' }}">Mingguan</a>
        <a href="{{ route('superadmin.laporan', ['periode' => 'bulanan']) }}" class="sa-period-tab {{ $periode === 'bulanan' ? 'active' : '' }}">Bulanan</a>
        <a href="{{ route('superadmin.laporan', ['periode' => 'tahunan']) }}" class="sa-period-tab {{ $periode === 'tahunan' ? 'active' : '' }}">Tahunan</a>
    </div>

    {{-- STAT CARDS --}}
    <div class="sa-lap-stats">
        <div class="sa-lap-stat">
            <div class="sa-lap-stat-icon green"><i class="fas fa-coins"></i></div>
            <div>
                <span class="sa-lap-stat-label">Total Pendapatan</span>
                <span class="sa-lap-stat-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="sa-lap-stat">
            <div class="sa-lap-stat-icon blue"><i class="fas fa-chart-bar"></i></div>
            <div>
                <span class="sa-lap-stat-label">Rata-rata Nilai Pesanan</span>
                <span class="sa-lap-stat-value">Rp {{ number_format($rataRataPesanan, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="sa-lap-stat">
            <div class="sa-lap-stat-icon red"><i class="fas fa-gavel"></i></div>
            <div>
                <span class="sa-lap-stat-label">Denda Terkumpul</span>
                <span class="sa-lap-stat-value">Rp {{ number_format($dendaTerkumpul, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    {{-- CHART ROW --}}
    <div class="sa-chart-row">
        <div class="sa-card sa-bar-chart-card">
            <div class="sa-card-header">
                <h2 class="sa-card-title">Pendapatan vs Target Bulanan</h2>
                <div class="sa-chart-legend">
                    <span class="sa-legend-item"><span class="sa-legend-dot dark"></span> ACTUAL</span>
                    <span class="sa-legend-item"><span class="sa-legend-dot light"></span> TARGET</span>
                </div>
            </div>
            <div class="sa-chart-container"><canvas id="saBarChart"></canvas></div>
            <div class="sa-chart-labels">
                @foreach($chartBulanan as $d)<span>{{ $d['label'] }}</span>@endforeach
            </div>
        </div>
        <div class="sa-card sa-donut-card">
            <h2 class="sa-card-title">METODE PEMBAYARAN</h2>
            <div class="sa-donut-container"><canvas id="saDonutChart"></canvas></div>
            <div class="sa-donut-legend" id="sa-donut-legend"></div>
        </div>
    </div>

    {{-- LOG TABLE --}}
    <div class="sa-card sa-log-card">
        <h2 class="sa-card-title">LOG PENYEWAAN DETAIL</h2>
        <table class="sa-log-table">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Jumlah Barang</th>
                    <th>Pendapatan</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                @php
                    $itemCount = $log->details->sum('jumlah');
                    $kategori = $log->details->first()?->product?->category?->nama_kategori ?? 'Umum';
                    $statusClass = match($log->status_transaksi) {
                        'selesai' => 'log-selesai',
                        'diproses' => 'log-diproses',
                        'dikirim' => 'log-dikirim',
                        'dibatalkan' => 'log-batal',
                        default => 'log-default',
                    };
                @endphp
                <tr>
                    <td>{{ $log->created_at->format('d M Y') }}</td>
                    <td>{{ $kategori }}</td>
                    <td>{{ $itemCount }} Pcs</td>
                    <td class="log-price">Rp {{ number_format($log->total_biaya, 0, ',', '.') }}</td>
                    <td><span class="log-status {{ $statusClass }}">{{ strtoupper($log->status_transaksi) }}</span></td>
                </tr>
                @empty
                <tr><td colspan="5" class="log-empty">Belum ada data log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartBulanan = @json($chartBulanan);
    const metodePembayaran = @json($metodePembayaran);

    // ── Bar Chart ──
    const barCanvas = document.getElementById('saBarChart');
    if (barCanvas) {
        const ctx = barCanvas.getContext('2d');
        function drawBar() {
            const c = barCanvas.parentElement;
            barCanvas.width = c.offsetWidth; barCanvas.height = 220;
            const w = barCanvas.width, h = barCanvas.height;
            const pad = { top: 20, right: 20, bottom: 10, left: 20 };
            const cw = w - pad.left - pad.right, ch = h - pad.top - pad.bottom;
            ctx.clearRect(0, 0, w, h);
            const allVals = chartBulanan.flatMap(d => [d.actual, d.target]);
            const mx = Math.max(...allVals, 1);
            const groupW = cw / chartBulanan.length;
            const barW = Math.min(24, groupW * 0.3);

            ctx.strokeStyle = 'rgba(0,0,0,0.06)'; ctx.lineWidth = 1;
            for (let i = 0; i <= 4; i++) {
                const y = pad.top + (ch / 4) * i;
                ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(w - pad.right, y); ctx.stroke();
            }

            chartBulanan.forEach((d, i) => {
                const cx = pad.left + groupW * i + groupW / 2;
                // Target bar (lighter)
                const th = (d.target / mx) * ch;
                const tx = cx - barW - 2;
                ctx.fillStyle = '#c8d6c5';
                roundRect(ctx, tx, pad.top + ch - th, barW, th, 3);
                // Actual bar
                const ah = (d.actual / mx) * ch;
                const ax = cx + 2;
                ctx.fillStyle = '#1a3a17';
                roundRect(ctx, ax, pad.top + ch - ah, barW, ah, 3);
            });
        }

        function roundRect(ctx, x, y, w, h, r) {
            ctx.beginPath();
            ctx.moveTo(x + r, y); ctx.lineTo(x + w - r, y);
            ctx.quadraticCurveTo(x + w, y, x + w, y + r);
            ctx.lineTo(x + w, y + h); ctx.lineTo(x, y + h);
            ctx.lineTo(x, y + r); ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath(); ctx.fill();
        }

        drawBar(); window.addEventListener('resize', drawBar);
    }

    // ── Donut Chart ──
    const donutCanvas = document.getElementById('saDonutChart');
    if (donutCanvas && metodePembayaran.length) {
        const dCtx = donutCanvas.getContext('2d');
        donutCanvas.width = 160; donutCanvas.height = 160;
        const cx = 80, cy = 80, outerR = 65, innerR = 40;
        const colors = ['#1a3a17', '#5a9e50', '#c8a951', '#9ca3af'];
        const total = metodePembayaran.reduce((s, m) => s + m.total, 0);
        let startAngle = -Math.PI / 2;

        metodePembayaran.forEach((m, i) => {
            const slice = (m.total / total) * Math.PI * 2;
            dCtx.beginPath();
            dCtx.arc(cx, cy, outerR, startAngle, startAngle + slice);
            dCtx.arc(cx, cy, innerR, startAngle + slice, startAngle, true);
            dCtx.closePath();
            dCtx.fillStyle = colors[i % colors.length];
            dCtx.fill();
            startAngle += slice;
        });

        // Legend
        const legend = document.getElementById('sa-donut-legend');
        const labels = { transfer: 'Transfer', cod: 'Di Toko', dp: 'DP', ewallet: 'E-Wallet' };
        metodePembayaran.forEach((m, i) => {
            const pct = Math.round((m.total / total) * 100);
            const label = labels[m.metode_pembayaran] || m.metode_pembayaran;
            legend.innerHTML += `<div class="sa-donut-item"><span class="sa-legend-dot" style="background:${colors[i % colors.length]}"></span><span>${label}</span><strong>${pct}%</strong></div>`;
        });
    }

    // Animations
    document.querySelectorAll('.sa-lap-stat').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(12px)';
        setTimeout(() => { c.style.transition = 'all 0.5s ease'; c.style.opacity = '1'; c.style.transform = 'translateY(0)'; }, 80 + i * 80);
    });
});
</script>
@endsection
