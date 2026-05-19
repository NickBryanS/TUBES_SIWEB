@extends('superadmin.layouts.superadmin')

@section('title', 'Dasber Eksekutif - Pemilik GKDL')
@section('sidebar-dashboard', 'active')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/superadmin/sa-dashboard.css') }}">
@endsection

@section('content')
<div class="sa-dashboard">
    {{-- STAT CARDS --}}
    <div class="sa-stats">
        <div class="sa-stat-card">
            <div class="sa-stat-icon sa-stat-icon-revenue"><i class="fas fa-chart-line"></i></div>
            <div class="sa-stat-body">
                @if($persenPendapatan != 0)
                <span class="sa-stat-change {{ $persenPendapatan >= 0 ? 'up' : 'down' }}">{{ $persenPendapatan >= 0 ? '+' : '' }}{{ $persenPendapatan }}%</span>
                @endif
                <span class="sa-stat-label">Pendapatan Hari Ini</span>
                <span class="sa-stat-value">Rp {{ number_format($pendapatanHariIni, 0, ',', '.') }}</span>
            </div>
        </div>
        <div class="sa-stat-card">
            <div class="sa-stat-icon sa-stat-icon-trx"><i class="fas fa-receipt"></i></div>
            <div class="sa-stat-body">
                <span class="sa-stat-label">Total Transaksi Aktif</span>
                <span class="sa-stat-value">{{ $totalTransaksiAktif }} <small>Sewa</small></span>
            </div>
        </div>
        <div class="sa-stat-card">
            <div class="sa-stat-icon sa-stat-icon-staf"><i class="fas fa-user-tie"></i></div>
            <div class="sa-stat-body">
                <span class="sa-stat-badge">Full Team</span>
                <span class="sa-stat-label">Karyawan Bertugas</span>
                <span class="sa-stat-value">{{ $totalStaf }} <small>Staf</small></span>
            </div>
        </div>
        <div class="sa-stat-card {{ $stokTipis > 0 ? 'sa-stat-danger' : '' }}">
            <div class="sa-stat-icon sa-stat-icon-stok"><i class="fas fa-cubes"></i></div>
            <div class="sa-stat-body">
                @if($stokTipis > 0)
                <span class="sa-stat-badge sa-stat-badge-danger">Stok Tipis</span>
                @endif
                <span class="sa-stat-label">Total Barang Disewa</span>
                <span class="sa-stat-value">{{ $totalBarang }} <small>Unit</small></span>
            </div>
        </div>
    </div>

    {{-- MIDDLE ROW: Chart + Top Products --}}
    <div class="sa-middle-grid">
        <div class="sa-card sa-chart-card">
            <div class="sa-card-header">
                <h2 class="sa-card-title">Tren Penyewaan</h2>
                <span class="sa-card-subtitle">Analisis 7 hari & Tren terbaik</span>
            </div>
            <div class="sa-chart-container"><canvas id="saRevenueChart"></canvas></div>
            <div class="sa-chart-labels">
                @foreach($chartData as $d)<span>{{ $d['label'] }}</span>@endforeach
            </div>
        </div>
        <div class="sa-card sa-top-products">
            <h2 class="sa-card-title">5 Produk Terlaris</h2>
            <div class="sa-top-list">
                @forelse($topProduk as $i => $p)
                <div class="sa-top-item">
                    <div class="sa-top-rank">
                        <div class="sa-top-img">
                            @if($p->url_gambar)
                            <img src="{{ asset('storage/' . $p->url_gambar) }}" alt="{{ $p->nama_produk }}">
                            @else
                            <i class="fas fa-campground"></i>
                            @endif
                        </div>
                    </div>
                    <div class="sa-top-info">
                        <span class="sa-top-name">{{ $p->nama_produk }}</span>
                    </div>
                    <span class="sa-top-count">{{ $p->total_sewa }}x</span>
                </div>
                @empty
                <p class="sa-empty-text">Belum ada data.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- BOTTOM ROW: Fleet + Inventory Warning + Insight --}}
    <div class="sa-bottom-grid">
        <div class="sa-card sa-fleet-card">
            <div class="sa-card-header">
                <h2 class="sa-card-title">Status Armada & Distribusi</h2>
                <span class="sa-status-dot-online"></span>
                <span class="sa-online-label">Online</span>
            </div>
            <div class="sa-fleet-list">
                @forelse($armadaAktif as $arm)
                <div class="sa-fleet-item">
                    <div class="sa-fleet-avatar">
                        <span>{{ strtoupper(substr($arm->user->nama_lengkap ?? 'U', 0, 1)) }}</span>
                    </div>
                    <div class="sa-fleet-info">
                        <span class="sa-fleet-name">{{ $arm->user->nama_lengkap ?? 'Kurir' }}</span>
                        <span class="sa-fleet-status"><i class="fas fa-circle"></i> Menuju {{ Str::limit($arm->alamat_pengiriman ?? 'Tujuan', 30) }}</span>
                    </div>
                    <span class="sa-fleet-distance">{{ rand(2, 15) }} Km</span>
                </div>
                @empty
                <p class="sa-empty-text"><i class="fas fa-truck"></i> Tidak ada armada aktif saat ini.</p>
                @endforelse
            </div>
            <div class="sa-fleet-map">
                <span class="sa-fleet-map-label">Live Map: Bandung Utara</span>
            </div>
        </div>

        <div class="sa-right-stack">
            <div class="sa-card sa-warning-card">
                <h2 class="sa-card-title"><i class="fas fa-triangle-exclamation sa-warning-icon"></i> Peringatan Inventaris</h2>
                <div class="sa-warning-list">
                    @forelse($peringatanStok as $ps)
                    <div class="sa-warning-item">
                        <div class="sa-warning-dot {{ $ps->stok_tersedia <= 2 ? 'danger' : 'warning' }}"></div>
                        <div class="sa-warning-info">
                            <span class="sa-warning-name">{{ $ps->nama_produk }} perlu restock</span>
                            <span class="sa-warning-detail">Sisa {{ $ps->stok_tersedia }} dari {{ $ps->total_stok }} unit</span>
                        </div>
                    </div>
                    @empty
                    <p class="sa-empty-text">Semua stok aman.</p>
                    @endforelse
                </div>
            </div>

            <div class="sa-insight-card">
                <div class="sa-insight-icon"><i class="fas fa-lightbulb"></i></div>
                <h3 class="sa-insight-title">Business Insight</h3>
                <p class="sa-insight-text">Pendapatan meningkat {{ abs($persenPendapatan) }}% di akhir pekan. Coba tambahkan paket di hari Sabtu untuk mendapatkan penyewaan lebih banyak dan optimalkan stok paling laris.</p>
                <button class="sa-insight-btn" type="button">Lihat Rekomendasi</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ── Bar Chart ──
    const canvas = document.getElementById('saRevenueChart');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const chartData = @json($chartData);

    function resizeCanvas() {
        const c = canvas.parentElement;
        canvas.width = c.offsetWidth;
        canvas.height = 200;
        drawChart();
    }

    function drawChart() {
        const w = canvas.width, h = canvas.height;
        const pad = { top: 20, right: 30, bottom: 10, left: 20 };
        const cw = w - pad.left - pad.right, ch = h - pad.top - pad.bottom;
        ctx.clearRect(0, 0, w, h);
        const vals = chartData.map(d => d.value);
        const mx = Math.max(...vals, 1);
        const barW = Math.min(40, (cw / vals.length) * 0.55);
        const gap = (cw - barW * vals.length) / (vals.length + 1);

        // Grid lines
        ctx.strokeStyle = 'rgba(0,0,0,0.06)'; ctx.lineWidth = 1;
        for (let i = 0; i <= 4; i++) {
            const y = pad.top + (ch / 4) * i;
            ctx.beginPath(); ctx.moveTo(pad.left, y); ctx.lineTo(w - pad.right, y); ctx.stroke();
        }

        // Bars
        vals.forEach((v, i) => {
            const barH = (v / mx) * ch;
            const x = pad.left + gap + i * (barW + gap);
            const y = pad.top + ch - barH;
            const isMax = v === mx;

            const grad = ctx.createLinearGradient(x, y, x, pad.top + ch);
            if (isMax) {
                grad.addColorStop(0, '#2D5A27'); grad.addColorStop(1, '#1a3a17');
            } else {
                grad.addColorStop(0, '#c8d6c5'); grad.addColorStop(1, '#a8b8a5');
            }

            ctx.beginPath();
            const r = 4;
            ctx.moveTo(x + r, y); ctx.lineTo(x + barW - r, y);
            ctx.quadraticCurveTo(x + barW, y, x + barW, y + r);
            ctx.lineTo(x + barW, pad.top + ch);
            ctx.lineTo(x, pad.top + ch);
            ctx.lineTo(x, y + r);
            ctx.quadraticCurveTo(x, y, x + r, y);
            ctx.closePath();
            ctx.fillStyle = grad; ctx.fill();
        });
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    // Animations
    document.querySelectorAll('.sa-stat-card').forEach((c, i) => {
        c.style.opacity = '0'; c.style.transform = 'translateY(16px)';
        setTimeout(() => {
            c.style.transition = 'all 0.5s cubic-bezier(0.16,1,0.3,1)';
            c.style.opacity = '1'; c.style.transform = 'translateY(0)';
        }, 80 + i * 90);
    });

    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.style.transition = 'all 0.6s cubic-bezier(0.16,1,0.3,1)';
                e.target.style.opacity = '1'; e.target.style.transform = 'translateY(0)';
                obs.unobserve(e.target);
            }
        });
    }, { threshold: 0.1 });
    document.querySelectorAll('.sa-card, .sa-insight-card').forEach(c => {
        c.style.opacity = '0'; c.style.transform = 'translateY(16px)'; obs.observe(c);
    });

    const al = document.getElementById('admin-alert');
    if (al) { setTimeout(() => { al.style.opacity = '0'; setTimeout(() => al.remove(), 300); }, 4000); }
});
</script>
@endsection
