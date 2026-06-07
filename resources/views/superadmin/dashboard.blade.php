@extends('superadmin.layouts.superadmin')

@section('title', 'Dashboard Pemilik - Gardakala Outdoor')
@section('sidebar-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-dashboard.css') }}">
@endsection

@section('content')
<div class="sa-dash">

    {{-- ── PAGE HEADER ── --}}
    <div class="sa-dash-header">
        <div>
            <p class="sa-dash-greeting">Selamat {{ now()->hour < 12 ? 'Pagi' : (now()->hour < 17 ? 'Siang' : 'Malam') }}, {{ Auth::user()->nama_lengkap ?? 'Pemilik' }} 👋</p>
            <h1 class="sa-dash-title">Ringkasan Bisnis Hari Ini</h1>
            <p class="sa-dash-sub">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <a href="{{ route('superadmin.laporan') }}" class="sa-dash-btn-laporan">
            <i class="fas fa-chart-bar"></i> Lihat Laporan Lengkap
        </a>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div class="sa-dash-stats">

        {{-- Card 1: Pendapatan Hari Ini --}}
        <div class="sa-stat-card sa-stat-revenue" id="stat-revenue">
            <div class="sa-stat-icon-wrap revenue">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="sa-stat-info">
                <span class="sa-stat-label">Pendapatan Hari Ini</span>
                <span class="sa-stat-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</span>
                <div class="sa-stat-compare">
                    @if($persenHarian != 0)
                        <span class="sa-stat-badge {{ $persenHarian >= 0 ? 'badge-up' : 'badge-down' }}">
                            <i class="fas fa-arrow-{{ $persenHarian >= 0 ? 'up' : 'down' }}"></i>
                            {{ $persenHarian >= 0 ? '+' : '' }}{{ $persenHarian }}%
                        </span>
                        <span class="sa-stat-vs">vs kemarin</span>
                    @else
                        <span class="sa-stat-vs">Kemarin: Rp {{ number_format($pendapatanKemarin, 0, ',', '.') }}</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 2: Penyewaan Aktif --}}
        <div class="sa-stat-card" id="stat-aktif">
            <div class="sa-stat-icon-wrap aktif">
                <i class="fas fa-campground"></i>
            </div>
            <div class="sa-stat-info">
                <span class="sa-stat-label">Penyewaan Aktif</span>
                <span class="sa-stat-value">{{ $penyewaanAktif }} <small>Transaksi</small></span>
                <span class="sa-stat-sub">{{ $totalAlatDisewa }} alat sedang disewa</span>
            </div>
        </div>

        {{-- Card 3: Perlu Perhatian --}}
        <div class="sa-stat-card {{ $totalPerhatian > 0 ? 'sa-stat-warning' : '' }}" id="stat-perhatian">
            <div class="sa-stat-icon-wrap perhatian">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="sa-stat-info">
                <span class="sa-stat-label">Perlu Perhatian</span>
                <span class="sa-stat-value">{{ $totalPerhatian }} <small>Item</small></span>
                <div class="sa-stat-detail-list">
                    @if($menungguVerifikasi > 0)
                        <span class="sa-detail-tag">{{ $menungguVerifikasi }} pesanan menunggu</span>
                    @endif
                    @if($pembayaranPending > 0)
                        <span class="sa-detail-tag">{{ $pembayaranPending }} pembayaran pending</span>
                    @endif
                    @if($stokTipis > 0)
                        <span class="sa-detail-tag">{{ $stokTipis }} stok menipis</span>
                    @endif
                    @if($totalPerhatian == 0)
                        <span class="sa-detail-tag tag-ok">Semua aman ✓</span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Card 4: Pendapatan Bulan Ini --}}
        <div class="sa-stat-card" id="stat-bulanan">
            <div class="sa-stat-icon-wrap bulanan">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="sa-stat-info">
                <span class="sa-stat-label">Pendapatan Bulan Ini</span>
                <span class="sa-stat-value">Rp {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</span>
                <span class="sa-stat-sub">{{ $totalStaf }} admin aktif beroperasi</span>
            </div>
        </div>
    </div>

    {{-- ── MAIN CONTENT GRID ── --}}
    <div class="sa-dash-grid">

        {{-- LEFT COLUMN --}}
        <div class="sa-dash-left">

            {{-- Aktivitas Terkini --}}
            <div class="sa-card" id="card-aktivitas">
                <div class="sa-card-head">
                    <h2 class="sa-card-title"><i class="fas fa-bolt"></i> Aktivitas Terkini</h2>
                    <a href="{{ route('superadmin.activity-log') }}" class="sa-card-link">Lihat Semua <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="sa-timeline">
                    @forelse($aktivitasTerkini as $act)
                    <div class="sa-timeline-item">
                        <div class="sa-timeline-dot" style="background: {{ $act['color'] }}">
                            <i class="fas {{ $act['icon'] }}"></i>
                        </div>
                        <div class="sa-timeline-content">
                            <div class="sa-timeline-top">
                                <span class="sa-timeline-label" style="color: {{ $act['color'] }}">{{ $act['label'] }}</span>
                                <span class="sa-timeline-id">{{ $act['order_id'] }}</span>
                            </div>
                            <p class="sa-timeline-msg">{{ $act['message'] }}</p>
                            <div class="sa-timeline-bottom">
                                <span class="sa-timeline-amount">Rp {{ number_format($act['total'], 0, ',', '.') }}</span>
                                <span class="sa-timeline-time">{{ $act['time']->diffForHumans() }}</span>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="sa-empty-state">
                        <i class="fas fa-inbox"></i>
                        <p>Belum ada aktivitas hari ini.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Aliran Kas 7 Hari --}}
            <div class="sa-card" id="card-chart">
                <div class="sa-card-head">
                    <h2 class="sa-card-title"><i class="fas fa-chart-area"></i> Aliran Kas 7 Hari</h2>
                </div>
                <div class="sa-chart-wrap">
                    <canvas id="cashFlowChart"></canvas>
                </div>
                <div class="sa-chart-labels">
                    @foreach($chartData as $d)<span>{{ $d['label'] }}</span>@endforeach
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN --}}
        <div class="sa-dash-right">



            {{-- Barang Terlaris --}}
            <div class="sa-card" id="card-terlaris">
                <div class="sa-card-head">
                    <h2 class="sa-card-title"><i class="fas fa-fire"></i> Barang Terlaris</h2>
                </div>
                <div class="sa-terlaris-list">
                    @forelse($barangTerlaris as $i => $item)
                    <div class="sa-terlaris-item">
                        <span class="sa-terlaris-rank {{ $i < 3 ? 'rank-top' : '' }}">{{ $i + 1 }}</span>
                        <div class="sa-terlaris-info">
                            <span class="sa-terlaris-name">{{ $item->product->nama_produk ?? '-' }}</span>
                            <span class="sa-terlaris-cat">{{ $item->product->category->nama_kategori ?? 'Umum' }}</span>
                        </div>
                        <span class="sa-terlaris-count">{{ $item->total_sewa }}× disewa</span>
                    </div>
                    @empty
                    <div class="sa-empty-state small">
                        <i class="fas fa-box-open"></i>
                        <p>Belum ada data penyewaan.</p>
                    </div>
                    @endforelse
                </div>
            </div>
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

        function curve(p) {
            ctx.beginPath(); ctx.moveTo(p[0].x, p[0].y);
            for (let i = 0; i < p.length - 1; i++) {
                const cx1 = p[i].x + (p[i+1].x - p[i].x) / 3, cy1 = p[i].y;
                const cx2 = p[i+1].x - (p[i+1].x - p[i].x) / 3, cy2 = p[i+1].y;
                ctx.bezierCurveTo(cx1, cy1, cx2, cy2, p[i+1].x, p[i+1].y);
            }
        }

        const grad = ctx.createLinearGradient(0, pad.top, 0, h);
        grad.addColorStop(0, 'rgba(45,90,39,0.22)');
        grad.addColorStop(1, 'rgba(45,90,39,0.01)');
        curve(pts);
        ctx.lineTo(pts[pts.length - 1].x, pad.top + ch);
        ctx.lineTo(pts[0].x, pad.top + ch);
        ctx.closePath(); ctx.fillStyle = grad; ctx.fill();

        curve(pts); ctx.strokeStyle = '#2D5A27'; ctx.lineWidth = 2.5; ctx.stroke();

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
    fadeUp(document.querySelectorAll('.sa-stat-card'), 0);

    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.transition = 'all 0.6s cubic-bezier(0.16,1,0.3,1)';
                e.target.style.opacity = '1'; e.target.style.transform = 'translateY(0)';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.sa-card').forEach(el => {
        el.style.opacity = '0'; el.style.transform = 'translateY(18px)';
        obs.observe(el);
    });

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
