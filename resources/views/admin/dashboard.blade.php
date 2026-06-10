@extends('admin.layouts.admin')

@section('title', 'Dasbor Admin - Garkadala Outdoor')
@section('sidebar-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin/admin-dashboard.css') }}">
@endsection

@section('content')
<div class="admin-dashboard">

    {{-- ── PAGE HEADER ── --}}
    <div class="dash-page-header">
        <div class="dash-page-title-group">
            <h1 class="dash-page-label">Laporan Keuangan</h1>
            <p class="dash-page-sub">Pantau performa bisnis dan arus kas Gardakala Outdoor secara real-time.</p>
        </div>
        <a href="{{ route('admin.dashboard.export', ['period' => $period]) }}" class="dash-btn-ekspor">
            <i class="fas fa-download"></i> Ekspor {{ ucfirst($period) }}
        </a>
    </div>

    {{-- ── PERIOD TABS & FILTER TANGGAL ── --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div class="period-tabs" style="margin-bottom: 0;">
            <a href="{{ route('admin.dashboard', ['period' => 'mingguan']) }}"
               class="period-tab {{ $period === 'mingguan' ? 'active' : '' }}">Mingguan</a>
            <a href="{{ route('admin.dashboard', ['period' => 'bulanan']) }}"
               class="period-tab {{ $period === 'bulanan' ? 'active' : '' }}">Bulanan</a>
            <a href="{{ route('admin.dashboard', ['period' => 'tahunan']) }}"
               class="period-tab {{ $period === 'tahunan' ? 'active' : '' }}">Tahunan</a>
        </div>

        <form action="{{ route('admin.dashboard.export') }}" method="GET" style="display: flex; gap: 8px; align-items: center;">
            <div style="display: flex; align-items: center; background: #fff; padding: 6px 12px; border-radius: 8px; border: 1px solid #e5e7eb; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <input type="date" name="start_date" style="border:none; background:transparent; font-size:13px; outline:none; color: #4b5563; font-family: inherit;" required>
                <span style="color:#9ca3af; font-size:13px;">s/d</span>
                <input type="date" name="end_date" style="border:none; background:transparent; font-size:13px; outline:none; color: #4b5563; font-family: inherit;" required>
            </div>
            <button type="submit" class="dash-btn-ekspor" style="padding: 8px 16px; border:none; cursor:pointer; font-size:13px; height: 36px;">
                <i class="fas fa-file-pdf"></i> Ekspor Custom
            </button>
        </form>
    </div>

    {{-- ── STAT CARDS ROW ── --}}
    <div class="dash-stats-row">
        {{-- Total Pendapatan (dark card) --}}
        <div class="dash-stat-card card-dark" id="stat-pendapatan">
            <div class="stat-label-sm">TOTAL PENDAPATAN</div>
            <div class="stat-big-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            @if($persenPerubahan != 0)
            <div class="stat-growth">
                <i class="fas fa-arrow-{{ $persenPerubahan >= 0 ? 'up' : 'down' }}"></i>
                {{ $persenPerubahan >= 0 ? '+' : '' }}{{ $persenPerubahan }}% dari periode lalu
            </div>
            @else
            <div class="stat-growth" style="color:rgba(255,255,255,0.4);">
                <i class="fas fa-minus"></i> Tidak ada perubahan
            </div>
            @endif
        </div>

        {{-- Pesanan Selesai (light card) --}}
        <div class="dash-stat-card card-light" id="stat-selesai">
            <div class="stat-label-sm">PESANAN SELESAI</div>
            <div class="stat-big-value">{{ number_format($pesananSelesai) }} <span style="font-size:1rem;font-weight:500;color:#6b7280;">Transaksi</span></div>
            <div class="stat-light-sub">
                <span class="dot-green"></span>
                {{ $tingkatKepuasan }}% tingkat kepuasan
            </div>
        </div>

        {{-- Saldo Tersedia (light card) --}}
        <div class="dash-stat-card card-light" id="stat-saldo">
            <div class="stat-label-sm">SALDO TERSEDIA</div>
            <div class="stat-big-value">Rp {{ number_format($saldoTersedia, 0, ',', '.') }}</div>
            <a href="{{ route('admin.transaksi.index', ['status' => 'selesai']) }}" class="stat-saldo-link">
                Lihat Transaksi <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

    {{-- ── RINCIAN TRANSAKSI TABLE ── --}}
    <div class="dash-table-card" id="dash-table-card">
        <div class="dash-table-header">
            <h2 class="dash-table-title">Rincian Transaksi</h2>
        </div>

        <table class="dash-trx-table">
            <thead>
                <tr>
                    <th>TANGGAL</th>
                    <th>ID PESANAN</th>
                    <th>PENYEWA</th>
                    <th>TOTAL</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTransaksi as $t)
                @php
                    $statusMap = [
                        'menunggu'       => ['label' => 'MENUNGGU',  'class' => 'status-menunggu'],
                        'menunggu_admin' => ['label' => 'VERIFIKASI','class' => 'status-menunggu-admin'],
                        'diproses'       => ['label' => 'PROSES',    'class' => 'status-diproses'],
                        'dikirim'        => ['label' => 'DIKIRIM',   'class' => 'status-dikirim'],
                        'selesai'        => ['label' => 'SELESAI',   'class' => 'status-selesai'],
                        'dibatalkan'     => ['label' => 'BATAL',     'class' => 'status-dibatalkan'],
                    ];
                    $st = $statusMap[$t->status_transaksi] ?? ['label' => strtoupper($t->status_transaksi), 'class' => 'status-menunggu'];
                @endphp
                <tr>
                    <td class="trx-date">{{ \Carbon\Carbon::parse($t->created_at)->translatedFormat('d M Y') }}</td>
                    <td class="trx-id">#GKD-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</td>
                    <td>{{ $t->user->nama_lengkap ?? '-' }}</td>
                    <td class="trx-total">Rp {{ number_format($t->total_biaya, 0, ',', '.') }}</td>
                    <td><span class="trx-status {{ $st['class'] }}">{{ $st['label'] }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:32px;color:#9ca3af;">
                        <i class="fas fa-receipt" style="font-size:1.5rem;margin-bottom:8px;display:block;"></i>
                        Belum ada transaksi.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="dash-table-footer">
            <span>Menampilkan {{ $recentTransaksi->firstItem() ?? 0 }}–{{ $recentTransaksi->lastItem() ?? 0 }} dari {{ $recentTransaksi->total() }} transaksi</span>
            <div class="dash-pagination">
                @if($recentTransaksi->onFirstPage())
                    <span class="dash-pg-btn pg-nav" style="opacity:0.4;cursor:default;"><i class="fas fa-chevron-left"></i></span>
                @else
                    <a class="dash-pg-btn pg-nav" href="{{ $recentTransaksi->previousPageUrl() }}"><i class="fas fa-chevron-left"></i></a>
                @endif

                @foreach($recentTransaksi->getUrlRange(1, min($recentTransaksi->lastPage(), 5)) as $page => $url)
                    <a class="dash-pg-btn {{ $page == $recentTransaksi->currentPage() ? 'pg-active' : '' }}" href="{{ $url }}">{{ $page }}</a>
                @endforeach

                @if($recentTransaksi->hasMorePages())
                    <a class="dash-pg-btn pg-nav" href="{{ $recentTransaksi->nextPageUrl() }}"><i class="fas fa-chevron-right"></i></a>
                @else
                    <span class="dash-pg-btn pg-nav" style="opacity:0.4;cursor:default;"><i class="fas fa-chevron-right"></i></span>
                @endif
            </div>
        </div>
    </div>

    {{-- ── BOTTOM ROW: CHART + PAYMENT ── --}}
    <div class="dash-bottom-row">

        {{-- Aliran Kas Chart --}}
        <div class="dash-chart-card" id="dash-chart-card">
            <div class="dash-chart-header">
                <h2 class="dash-chart-title">Aliran Kas Bulanan</h2>
                <div class="dash-chart-period">
                    {{ \Carbon\Carbon::now()->translatedFormat('F Y') }} <i class="fas fa-chevron-down"></i>
                </div>
            </div>
            <div class="dash-chart-wrap">
                <canvas id="cashFlowChart"></canvas>
            </div>
            <div class="dash-chart-labels">
                @foreach($chartData as $d)<span>{{ $d['label'] }}</span>@endforeach
            </div>
        </div>

        {{-- Metode Pembayaran --}}
        <div class="dash-payment-card" id="dash-payment-card">
            <h3 class="dash-payment-title">Metode Pembayaran</h3>
            <div class="dash-payment-list">
                @foreach($paymentBreakdown as $pb)
                <div class="dash-payment-item">
                    <div class="dash-payment-row">
                        <span class="dash-payment-name">{{ $pb['label'] }}</span>
                        <span class="dash-payment-pct">{{ $pb['pct'] }}%</span>
                    </div>
                    <div class="dash-payment-bar">
                        <div class="dash-payment-fill {{ $pb['fill'] }}" style="width:{{ $pb['pct'] }}%;"></div>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="dash-payment-quote">
                "Mayoritas pelanggan lebih memilih transfer bank untuk transaksi yang lebih aman dan efisien."
            </p>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Cash Flow Chart ──
    const canvas = document.getElementById('cashFlowChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const chartData = @json($chartData);

    function resizeCanvas() {
        const wrap = canvas.parentElement;
        canvas.width  = wrap.offsetWidth;
        canvas.height = wrap.offsetHeight || 200;
        drawChart();
    }

    function drawChart() {
        const w = canvas.width, h = canvas.height;
        const pad = { top: 20, right: 16, bottom: 10, left: 16 };
        const cw = w - pad.left - pad.right;
        const ch = h - pad.top - pad.bottom;
        ctx.clearRect(0, 0, w, h);

        const vals = chartData.map(d => d.value);
        const mx = Math.max(...vals, 1);
        const pts = vals.map((v, i) => ({
            x: pad.left + (i / (vals.length - 1 || 1)) * cw,
            y: pad.top + ch - (v / mx) * ch
        }));

        // Grid lines
        ctx.strokeStyle = 'rgba(0,0,0,0.05)'; ctx.lineWidth = 1;
        for (let i = 0; i <= 3; i++) {
            const y = pad.top + (ch / 3) * i;
            ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(w - pad.right, y); ctx.stroke();
        }

        if (pts.length < 2) return;

        // Curve function
        function curve(p) {
            ctx.beginPath(); ctx.moveTo(p[0].x, p[0].y);
            for (let i = 0; i < p.length - 1; i++) {
                const cx1 = p[i].x + (p[i+1].x - p[i].x) / 3, cy1 = p[i].y;
                const cx2 = p[i+1].x - (p[i+1].x - p[i].x) / 3, cy2 = p[i+1].y;
                ctx.bezierCurveTo(cx1, cy1, cx2, cy2, p[i+1].x, p[i+1].y);
            }
        }

        // Fill gradient
        const grad = ctx.createLinearGradient(0, pad.top, 0, h);
        grad.addColorStop(0, 'rgba(45,90,39,0.22)');
        grad.addColorStop(1, 'rgba(45,90,39,0.01)');
        curve(pts);
        ctx.lineTo(pts[pts.length - 1].x, pad.top + ch);
        ctx.lineTo(pts[0].x, pad.top + ch);
        ctx.closePath(); ctx.fillStyle = grad; ctx.fill();

        // Line
        curve(pts); ctx.strokeStyle = '#2D5A27'; ctx.lineWidth = 2.5; ctx.stroke();

        // Dots — highlight tallest bar
        const maxVal = Math.max(...vals);
        pts.forEach((pt, i) => {
            const isTall = vals[i] === maxVal && vals[i] > 0;
            ctx.beginPath(); ctx.arc(pt.x, pt.y, isTall ? 6 : 4, 0, Math.PI*2);
            ctx.fillStyle = isTall ? '#1c2b1a' : '#2D5A27'; ctx.fill();
            ctx.beginPath(); ctx.arc(pt.x, pt.y, isTall ? 3 : 2, 0, Math.PI*2);
            ctx.fillStyle = '#fff'; ctx.fill();
        });
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // ── Entrance Animations ──
    const fadeUp = (els, delay = 0, step = 80) => {
        els.forEach((el, i) => {
            el.style.opacity = '0'; el.style.transform = 'translateY(18px)';
            setTimeout(() => {
                el.style.transition = 'all 0.55s cubic-bezier(0.16,1,0.3,1)';
                el.style.opacity = '1'; el.style.transform = 'translateY(0)';
            }, delay + i * step);
        });
    };

    fadeUp(document.querySelectorAll('.dash-stat-card'), 0);

    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.transition = 'all 0.6s cubic-bezier(0.16,1,0.3,1)';
                e.target.style.opacity = '1'; e.target.style.transform = 'translateY(0)';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08 });

    [
        document.getElementById('dash-table-card'),
        document.getElementById('dash-chart-card'),
        document.getElementById('dash-payment-card'),
    ].filter(Boolean).forEach(el => {
        el.style.opacity = '0'; el.style.transform = 'translateY(18px)';
        obs.observe(el);
    });

    // ── Payment bars animate in ──
    setTimeout(() => {
        document.querySelectorAll('.dash-payment-fill').forEach(bar => {
            const w = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => { bar.style.width = w; }, 50);
        });
    }, 400);

    // ── Auto dismiss alerts ──
    const al = document.getElementById('admin-alert');
    if (al) {
        setTimeout(() => {
            al.style.opacity = '0'; al.style.transform = 'translateY(-10px)';
            setTimeout(() => al.remove(), 300);
        }, 4000);
    }
});
</script>
@endsection
